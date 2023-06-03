#!/usr/bin/perl

if ( $ENV{ 'REQUEST_METHOD' } eq 'POST' ) {
	exit;
}

use strict qw(subs vars);

$ENV{ 'HOME' } = '.';	# SSH needs access for $HOME/.ssh

use Time::Local;
use DBI;
use Net::IP;
use Net::IPAddress;
use Net::DNS;
use IO::Handle;
use IO::Socket;
use Net::SSH::Perl;
use Net::SSH::Perl::Cipher;
use Crypt::DES;
use Crypt::RSA;
use Crypt::DSA;
use URI::Escape;
use HTML::Entities;
use Net::SSH::Perl::Auth;

my $iStartResult = 0;
my $root_relative = '';
&udSetRoot;

my %valid_query = (
	'Cisco' => {
		'bgp'	=> "show ip bgp %s",
		'ping'	=> "ping %s",
		'trace'	=> "traceroute %s"
	},
	'Juniper' => {
		'bgp'	=> "show bgp %s",
		'ping'	=> "ping count 5 %s",
		'trace'	=> "traceroute %s"
	},
	'Unix' => {
		'bgp'	=> "show ip bgp %s",
		'ping'	=> "ping -c 5 %s",
		'trace' => "traceroute %s"
	}
);

# ud
my $page_script_dir = $root_relative.'/admin/';
my $page_script_suffix = '.js';
my @page_script = ( 'jquery', 'custom' );

my $page_style_dir = $root_relative.'/admin/';
my $page_style_suffix = '.css';
my @page_style = ( 'main' );

my %db_account = (
	'dsn' => '',#'dbi:mysql:lg',
	'username' => '',#'lg',
	'password' => '',#'ifah0beivoYahgu'
);
&udSetDbAccount;

my $hConnect;

my %arrCurrentLog = (
	'cr_date' => '',	# date of log
	'ip' => '',		# ip of client
	'agent' => '',		# agent for query
	'type' => '',		# type of query
	'addr' => '',		# address of query
	'query' => ''		# query sent to agent
);

my $lock_router = 2;		# seconds
my $szResolvedIp = '';
my %arrAgent = ();
my $iNumAgent = 0;
my $iCurrentAgent = 0;
my $lgurl = $root_relative.'/lg.cgi';
my $title = 'Looking Glass';
my $logolink = ".";
my $logoimage = $root_relative.'/admin/skin/logo.gif';
my $query_cmd = "";
my $bWhois = 0;
my %FORM = {};
#

$| = 1;

%FORM = &cgi_decode( $ENV{ 'QUERY_STRING' } );
&udConnect;
&udLoadAgent;
&udStartLog;

my $iRetCode = &udCheckAddr( $FORM{ 'addr' } );
if ( !$iRetCode ) {
	&udFinish( 1, 'Неверное значение поля: "Ip/Доменное имя"' );
} elsif ( $iRetCode == 3 ) {
	&udFinish( 1, 'Команда не может быть выполнена для данного адреса' );
}

if ( ( $FORM{ 'query' } ne '' ) && !( &udCheckQuery( $FORM{ 'query' } ) ) ) {
	&udFinish( 0, 'Неизвестный запрос' );
}

if ( ( $FORM{ 'query' } eq 'whois' ) && ( $FORM{ 'addr' } ne '' ) ) {
	$query_cmd = "whois %s";
	$bWhois = 1;
} elsif ( ( $FORM{ 'agent' } ne '' ) && ( $FORM{ 'query' } ne '' ) && ( $FORM{ 'addr' } ne '' ) ) {
	if ( !$iCurrentAgent ) {
		&udFinish( 0, 'Неизвестный агент' );
	} else {
		if ( ( $arrAgent{ $iCurrentAgent }{ 'ostype' } eq 'Unix' ) && ( $FORM{ 'query' } eq 'bgp' ) && ( $arrAgent{ $iCurrentAgent }{ 'password2' } eq '' ) ) {
			&udFinish( 0, 'Для выбранного агента невозможно выполнить запрос bgp' );
		} else {
			$query_cmd = $valid_query{ $arrAgent{ $iCurrentAgent }{ 'ostype' } }{ $FORM{ 'query' } };
		}
	}
} else {
	&udFinish;
}

if ( !$bWhois && $iCurrentAgent ) {
	$arrCurrentLog{ 'agent' } = $arrAgent{ $iCurrentAgent }{ 'name' };
}
$arrCurrentLog{ 'type' } = $FORM{ 'query' };
$arrCurrentLog{ 'addr' } = $FORM{ 'addr' };

my $command;

if ( !$bWhois && $FORM{ 'addr' } !~ /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/ ) {
	my $resolv = new Net::DNS::Resolver;
	my $query = $resolv->search( $FORM{ 'addr' } );
	if ( $query ) {
		foreach my $rr ( $query->answer ) {
			next unless $rr->type eq 'A';
			$szResolvedIp = ( $rr->address );
			last;
		}
	}
	if ( $szResolvedIp eq '' ) {
		&udFinish;
	}
} else {
	$szResolvedIp = $FORM{ 'addr' };
}

if ( !$bWhois ) {
	my $ip = new Net::IP( $szResolvedIp );
	if ( 'public' ne lc( $ip->iptype ) ) {
		&udFinish( 1, 'Команда не может быть выполнена для данного адреса' );
	}
}

$command = sprintf( $query_cmd, $szResolvedIp );

$arrCurrentLog{ 'query' } = $command;
&udSaveLog;

# command processing
&print_head( $command );

$FORM{ 'addr' } = '' if ( $FORM{ 'addr' } =~ /^[ ]*$/ );

if ( $arrAgent{ $iCurrentAgent }{ 'ostype' } eq 'Juniper' ) {
	if ( $command =~ /^show bgp n\w*\s+([\d\.A-Fa-f:]+)$/ ) {
		# show bgp n.. <IP> ---> show bgp neighbor <IP>
		$command = "show bgp neighbor $1";
	} elsif ( $command =~ /^show bgp n\w*\s+([\d\.A-Fa-f:]+) ro\w*$/ ) {
		# show bgp n.. <IP> ro.. ---> show route receive-protocol bgp <IP>
		$command = "show route receive-protocol bgp $1";
	} elsif ( $command =~ /^show bgp neighbors ([\d\.A-Fa-f:]+) routes all$/ ) {
		# show bgp neighbors <IP> routes all ---> show route receive-protocol bgp <IP> all
		$command = "show route receive-protocol bgp $1 all";
	} elsif ( $command =~ /^show bgp neighbors ([\d\.A-Fa-f:]+) routes damping suppressed$/ ) {
		# show bgp neighbors <IP> routes damping suppressed ---> show route receive-protocol bgp <IP> damping suppressed
		$command = "show route receive-protocol bgp $1 damping suppressed";
	} elsif ( $command =~ /^show bgp n\w*\s+([\d\.A-Fa-f:]+) advertised-routes ([\d\.A-Fa-f:\/]+)$/ ) {
		# show ip bgp n.. <IP> advertised-routes <prefix> ---> show route advertising-protocol bgp <IP> <prefix> exact detail
		$command = "show route advertising-protocol bgp $1 $2 exact detail";
	} elsif ( $command =~ /^show bgp n\w*\s+([\d\.A-Fa-f:]+) receive-protocol ([\d\.A-Fa-f:\/]+)$/ ) {
		# show ip bgp n.. <IP> receive-protocol <prefix> ---> show route receive-protocol bgp <IP> <prefix> exact detail
		$command = "show route receive-protocol bgp $1 $2 exact detail";
	} elsif ( $command =~ /^show bgp n\w*\s+([\d\.A-Fa-f:]+) a[\w\-]*$/ ) {
		# show ip bgp n.. <IP> a.. ---> show route advertising-protocol bgp <IP>
		$command = "show route advertising-protocol bgp $1";
	} elsif ( $command =~ /^show bgp\s+([\d\.A-Fa-f:]+\/\d+)$/ ) {
		# show bgp <IP>/mask ---> show route protocol bgp <IP> all
		$command = "show route protocol bgp $1 terse exact";
	} elsif ( $command =~ /^show bgp\s+([\d\.A-Fa-f:]+)$/ ) {
		# show bgp <IP> ---> show route protocol bgp <IP> all
		$command = "show route protocol bgp $1 terse";
	} elsif ( $command =~ /^show bgp\s+([\d\.A-Fa-f:\/]+) exact$/ ) {
		# show bgp <IP> exact ---> show route protocol bgp <IP> exact detail all
		$command = "show route protocol bgp $1 exact detail all";
	} elsif ( $command =~ /^show bgp re\s+(.*)$/ ) {
		# show ip bgp re <regexp> ---> show route aspath-regex <regexp> all
		my $re = $1;
		$re = "^.*${re}" if ($re !~ /^\^/);
		$re = "${re}.*\$" if ($re !~ /\$$/);
		$re =~ s/_/ /g;
		$command = "show route aspath-regex \"$re\" all";
	}
}

&print_form( $FORM{ 'query' }, $FORM{ 'addr' } );
if ( $bWhois ) {
	&udProcWhois( $command );
} else {
	my $tmp = &udBusyRouter( $FORM{ 'agent' } );
	if ( $tmp == 1 ) {
		print "<h2>Агент занят</h2>\n";
	} else {
		&udProcQuery( $command );
	}
}

&print_tail;
&udDisconnect;
exit;

# ud
sub udSetRoot {
	$_ = $ENV{ 'SCRIPT_NAME' };
	s/\/lg.cgi$//;
	$root_relative = $_;
}

sub udSetDbAccount {
	if ( -e 'db.php' ) {
		open( DBACCOUNT, 'db.php' );
		my $database = '';
		my $hostname = '';
		while( <DBACCOUNT> ) {
			if ( /"([^"]*)"\s=>\s"([^"]*)"/g ) {
				if ( $1 eq 'database' ) {
					$database = $2;
				} elsif ( $1 eq 'server' ) {
					$hostname = $2;
				} elsif ( $1 eq 'username' ) {
					$db_account{ 'username' } = $2;
				} elsif ( $1 eq 'password' ) {
					$db_account{ 'password' } = $2;
				}
			}
		}
		if ( $hostname eq '' ) {
			$hostname = 'localhost';
		}
		if ( $database ne '' ) {
			$db_account{ 'dsn' } = "DBI:mysql:database=$database;host=$hostname";
		}
		close( DBACCOUNT );
	} else {
		die( 'can\'t load database account file' );
	}
}

sub udConnect {
	$hConnect = DBI->connect( $db_account{ 'dsn' }, $db_account{ 'username' }, $db_account{ 'password' } ) || die( 'failed connection to database' );
	my $x = $hConnect->prepare( 'CREATE TABLE IF NOT EXISTS `ud_lquery` ( `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `cr_date` DATETIME, `name` VARCHAR( 255 ) )' );
	$x->execute;
}

sub udDisconnect {
	$hConnect->disconnect;
}

sub udLoadAgent {
	my $x = $hConnect->prepare( 'SELECT * FROM `ud_agent`' );
	$x->execute;
	if ( $x->rows > 0 ) {
		my $tmp;
		my $id;
		while( $tmp = $x->fetchrow_hashref ) {
			$id = $tmp->{ 'agent_id' };
			if ( $id ) {
				$arrAgent{ $id } = {
					'id'		=> $tmp->{ 'agent_id'		},
					'name'		=> $tmp->{ 'agent_name'		},
					'ip'		=> $tmp->{ 'agent_ip'		},
					'login'		=> $tmp->{ 'agent_login'	},
					'key'		=> $tmp->{ 'agent_key'		},
					'password'	=> $tmp->{ 'agent_password'	},
					'ostype'	=> $tmp->{ 'agent_ostype'	},
					'protocol'	=> $tmp->{ 'agent_protocol'	},
					'password2'	=> $tmp->{ 'agent_password2'	},
					'port'		=> $tmp->{ 'agent_port'		},
				};
			}
		}
		$iCurrentAgent = int( $FORM{ 'agent' } );
		if ( !defined( $arrAgent{ $iCurrentAgent } ) ) {
			$iCurrentAgent = 0;
		}
	}
}

sub udFinish {
	my ( $iSaveLog, $szMsg ) = @_;
	if ( $iSaveLog ) {
		$arrCurrentLog{ 'agent' } = $FORM{ 'router' };
		$arrCurrentLog{ 'type' } = 'illegal';
		$arrCurrentLog{ 'addr' } = $FORM{ 'addr' };
		&udSaveLog;
	}
	&print_head;
	&print_form;
	if ( defined( $szMsg ) && ( $szMsg ne "" ) ) {
		print '<h3>'.$szMsg.'</h3>';
	}
	&print_tail;
	&udDisconnect;
	exit;
}

sub udSaveLog {
	my $x = $hConnect->prepare( 'INSERT INTO `ud_log`(`log_cr_date`,`log_ip`,`log_agent`,`log_type`,`log_addr`,`log_query`) VALUE (?,?,?,?,?,?)' );
	$x->bind_param( 1, $arrCurrentLog{ 'cr_date' } );
	$x->bind_param( 2, $arrCurrentLog{ 'ip' } );
	$x->bind_param( 3, $arrCurrentLog{ 'agent' } );
	$x->bind_param( 4, $arrCurrentLog{ 'type' } );
	$x->bind_param( 5, $arrCurrentLog{ 'addr' } );
	$x->bind_param( 6, $arrCurrentLog{ 'query' } );
	$x->execute;
}

sub udSaveLogTime {
	my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst ) = localtime( time );
	$mon += 1;
	$year += 1900;
	my ( $szYear, $szMon, $szMday, $szHour, $szMin, $szSec);
	$szYear = ''.$year;
	if ( $mon < 10 ) {
		$szMon = '0'.$mon;
	} else {
		$szMon = ''.$mon;
	}
	if ( $mday < 10 ) {
		$szMday = '0'.$mday;
	} else {
		$szMday = ''.$mday;
	}
	if ( $hour < 10 ) {
		$szHour = '0'.$hour;
	} else {
		$szHour = ''.$hour;
	}
	if ( $min < 10 ) {
		$szMin = '0'.$min;
	} else {
		$szMin = ''.$min;
	}
	if ( $sec < 10 ) {
		$szSec = '0'.$sec;
	} else {
		$szSec = ''.$sec;
	}
	$arrCurrentLog{ 'cr_date' } = "$szYear-$szMon-$szMday $szHour:$szMin:$szSec";
}

sub udSaveLogIp {
	if ( $ENV{'REMOTE_ADDR'} ) {
		$arrCurrentLog{ 'ip' } = $ENV{'REMOTE_ADDR'};
	}
}

sub udStartLog {
	&udSaveLogTime;
	&udSaveLogIp;
}

sub udBusyRouter {
	my ( $curRouter ) = @_;
	my $x = $hConnect->prepare( "SELECT * FROM `ud_lquery` WHERE `name`=?" );
	$x->bind_param( 1, $curRouter );
	$x->execute;
	my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst ) = localtime( time );
	if ( $x->rows > 0 ) {
		my $tmp = $x->fetchrow_hashref;
		my ( $y, $m, $d, $h, $mn, $s ) = ( $tmp->{ 'cr_date' } =~ /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/s );
		$m -= 1;
		$y -= 1900;
		my $cur = timelocal( $sec, $min, $hour, $mday, $mon, $year );
		my $got = timelocal( $s, $mn, $h, $d, $m, $y );
		my $sec_diff = ( $cur - $got );
		if ( $sec_diff < $lock_router ) {
			return 1;
		}
		# unlock router
		$x = $hConnect->prepare( "DELETE FROM `ud_lquery` WHERE `name`=?" );
		$x->bind_param( 1, $curRouter );
		$x->execute;
	}
	# lock router
	$year += 1900;
	$mon += 1;
	$x = $hConnect->prepare( "INSERT INTO `ud_lquery`(`cr_date`,`name`) VALUES (?,?)" );
	$x->bind_param( 1, "$year-$mon-$mday $hour:$min:$sec" );
	$x->bind_param( 2, $curRouter );
	$x->execute;
	return 0;
}

sub udHeader {
	my ( $name, $content ) = @_;
	print $name.": ".$content;
}

sub udDocType {
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
}

sub udCheckQuery {
	my ( $query ) = @_;
	return( $query =~ /bgp|ping|trace|whois/ );
}

sub udCheckAddr {
	my $addr = shift;
	
	if ( $addr eq '' ) {
		return 1;
	}
	
	if ( $addr =~ /[^a-zA-Z0-9.\-]/ ) {
		return 0;
	}
	
	if ( $addr =~ /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/ ) {
		return 1;
	}
	
	if ( $addr =~ /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/ ) {
		return 1;
	}
	
	if ( ( $FORM{ 'query' } eq 'whois' ) ) {
		if ( $addr =~ /^AS(\d{1,5})/ig ) {
			my $iNum = int $1;
			if ( ( $iNum >= 0 ) && ( $iNum <= 64511 ) ) {
				return 2;
			}
		}
		return 3;
	}
	
	return 0;
}

sub udValidAddr {
	my ( $addr, $mask ) = @_;
	if ( $addr =~ /[^0-9.\/]/ ) {
		return 0;
	}
	if ( $mask ne '' ) {
		if ( ( $addr =~ /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/ )
			&&
		     ( $mask =~ /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/ ) ) {
			return 1;
		} else {
			return 0;
		}
	} else {
		if ( $addr =~ /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/ ) {
			return 1;
		} else {
			return 0;
		}
	}
}

sub udStyle {
	foreach ( @page_style ) {
		print '<link rel="stylesheet" type="text/css" href="'.$page_style_dir.$_.$page_style_suffix.'"/>';
	}
}

sub udScript {
	foreach ( @page_script ) {
		print "<script type=\"text/javascript\" src=\"".$page_script_dir.$_.$page_script_suffix."\"></script>";
	}
}

sub udProcString {
	my ( $str, $flt, $msk ) = @_;
	my @tmp = ();
	if ( @tmp = ( $str =~ /\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.)((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.)((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.)(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/g ) ) {
		my $v;
		my $i = 0;
		my @arrResult = ( );
		my @arrAdd = ( );
		foreach $v ( @tmp ) {
			if ( $v !~ /\./ ) {
				push @arrAdd, $v;
				++$i;
				if ( $i == 4 ) {
					my $x = join( ".", @arrAdd );
					push @arrResult, $x;
					$i = 0;
					@arrAdd = ( );
				}
			}
		}
		$i = 0;
		foreach $v ( @arrResult ) {
			if ( $msk eq '' ) {
				if ( $flt eq $v ) {
					$i = 1;
				}
			} elsif ( $flt eq ( mask( $v, $msk ) ) ) {
				$i = 1;
			}
		}
		if ( $i ) {
			print $str;
		}
	}
}

sub udFormatString {
	my ( $best, $count, $lastip ) = ( 0, 0, '' );
	my $str = shift;#$iStartResult
	$_ = $str;
	if ( $FORM{ 'query' } eq 'bgp' ) {
		my $flt = $FORM{ 'flt_inp' };
		my $msk = $FORM{ 'flt_msk' };
		if ( $FORM{ 'flt_reg' } ) {
			if ( ( $flt eq '' ) || /$flt/ ) {
				if ( $iStartResult == 0 ) {
					print '<div class="got_result" style="display: none;">&nbsp;</div>';
					$iStartResult = 1;
				}
				print;
			}
		} else {
			if ( ( $flt ne '' ) && &udValidAddr( $flt, $msk ) ) {
				if ( $iStartResult == 0 ) {
					print '<div class="got_result" style="display: none">&nbsp;</div>';
					$iStartResult = 1;
				}
				&udProcString( $_, $flt, $msk );
			} else {
				if ( $iStartResult == 0 ) {
					print '<div class="got_result" style="display: none">&nbsp;</div>';
					$iStartResult = 1;
				}
				print;
			}
		}
	} else {
		if ( $iStartResult == 0 ) {
			print '<div class="got_result" style="display: none">&nbsp;</div>';
			$iStartResult = 1;
		}
		print;
	}
}

sub print_head {
	my $t1 = shift;
	my $titlestr = $title;
	if ( $t1 ne '' ) {
		$titlestr .= " - $t1";
	}
	&udHeader( 'Content-Type', "text/html; charset=cp1251\n\n" );
	&udDocType;
	print "<html><head><title>$titlestr</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\"/>\n";
	&udStyle;
	&udScript;
	print '</head><body id="main1">';
	print "\n";
	print '<div class="header"><a href="'.$logolink.'"><img src="'.$logoimage.'" alt="'.$titlestr.'" title="'.$titlestr.'"/></a></div>';
	print '<p><a href="'.$root_relative.'/help/">Помощь</a></p>';
}

sub udPrintQueries {
	print '<div id="rad_list">';
	my %queries = (
		'bgp' => 1,
		'ping' => 0,
		'trace' => 0,
		'whois' => 0
	);
	if ( ( $FORM{ 'query' } ne '' ) && exists( $queries{ $FORM{ 'query' } } ) ) {
		$queries{ "bgp" } = 0;
		$queries{ $FORM{ 'query' } } = 1;
	}
	my $myValue;
	my $myIndex;
	foreach $myIndex ( sort( keys( %queries ) ) ) {
		$myValue = $queries{ $myIndex };
		print '<input id="rad'.$myIndex.'" type="radio" name="query" value="'.$myIndex.'"';
		if ( $myValue == 1 ) {
			print ' checked="checked"';
		}
		print '/>&nbsp;<label for="rad'.$myIndex.'">'.$myIndex.'</label>&nbsp;&nbsp;';
	}
	print '</div>';
}

sub udPrintFilter {
	my $flt = $FORM{ 'flt_inp' };
	my $mask = $FORM{ 'flt_msk' };
	#print '<tr><td class="lbl">&nbsp;</td><td class="inp"><div>Отфильтровать результаты по:</div></td></tr>';
	print '<tr><td class="lbl"><div>Ip/Сеть/Regexp:</div></td><td class="inp"><div><input id="flt" type="text" class="text"  name="flt_inp" value="'.encode_entities( $flt ).'"';
	if ( ( $FORM{ 'query' } ne '' ) && ( $FORM{ 'query' } ne 'bgp' ) ) {
		print ' disabled="disabled"';
	}
	print '/>&nbsp;<input id="reg" type="checkbox" name="flt_reg" value="1"';
	if ( ( $FORM{ 'query' } ne '' ) && ( $FORM{ 'query' } ne 'bgp' ) ) {
		print ' disabled="disabled"';
	} elsif ( $FORM{ 'flt_reg' } ) {
		print ' checked="checked"';
	}
	print '/>&nbsp;<label for="reg">regexp</label><span class="info">Если вы используете перл совместимое регулярное выражение, поставьте галочку regexp</span></div></td></tr>'."\n";
	print '<tr><td class="lbl"><div>Маска сети:</div></td><td class="inp"><div><input id="msk" type="text" class="text" name="flt_msk" value="'.encode_entities( $mask ).'"';
	if ( ( $FORM{ 'query' } ne "" ) && ( $FORM{ 'query' } ne "bgp" ) ) {
		print ' disabled="disabled"';
	}
	print '/></div></td></tr>'."\n";
}

sub udPrintAgent {
	print '<select id="agent" name="agent"';
	if ( $FORM{ 'query' } eq "whois" ) {
		print " disabled=\"disabled\"";
	}
	print ">\n";
	foreach my $i ( sort( keys( %arrAgent ) ) ) {
		if ( $i ) {
			print '<option value="'.$arrAgent{ $i }{ 'id' }.'"';
			my $id = int( $arrAgent{ $i }{ 'id' } );
			if ( $iCurrentAgent == $id ) {
				print ' selected="selected"';
			}
			print '>'.html_encode( $arrAgent{ $i }{ 'name' } )."\n";
		}
	}
	print "</select>\n";
}

sub udProcWhois {
	my $cmd = shift;
	print '<script type="text/javascript">if ( ShowLoading ) ShowLoading( );</script>';
	print '<pre id="result">';
	system( $cmd." 2>&1" );
	print '</pre>';
}

sub udProcQuery {
	my $cmd = shift;
	print '<script type="text/javascript">if ( ShowLoading ) ShowLoading( );</script>';
	print "<b>Command:</b> ".html_encode( $cmd )."<br/>\n";
	print "<h3>Result:</h3>";
	print '<pre id="result">';
	die $@ if $@;
	my $host = $arrAgent{ $iCurrentAgent }{ 'ip' };
	my $login = $arrAgent{ $iCurrentAgent }{ 'login' };
	my $password = $arrAgent{ $iCurrentAgent }{ 'password' };
	my $protocol = $arrAgent{ $iCurrentAgent }{ 'protocol' };
	my $ostype = $arrAgent{ $iCurrentAgent }{ 'ostype' };
	if ( ( $ostype eq 'Unix' ) && ( $cmd =~ /bgp/ ) ) {
		eval "
			use Net::Telnet;
		";
		my $timeout = 30;
		my $password2 = $arrAgent{ $iCurrentAgent }{ 'password2' };
		my $port = $arrAgent{ $iCurrentAgent }{ 'port' };
		my $telnet = new Net::Telnet;
		$telnet->port( $port );
		$telnet->errmode( sub { print "ERROR:" . join('|', @_) . "\n"; } );
		$telnet->timeout( $timeout );
		$telnet->option_callback( sub { return; } );
		$telnet->option_accept(Do => 31);		# TELOPT_NAWS
		$telnet->open( Host => $host,
		               Port => $port );

		if ($password2 ne '') {
			$telnet->waitfor('/word:.*$/');
			$telnet->print("$password2");
		}

		$telnet->waitfor(Match => '/.*[\$%>] {0,1}$/',
		                 Match => '/^[^#]*[\$%#>] {0,1}$/');

		$telnet->telnetmode(0);
		$telnet->put(pack("C9",
		                  255,			# TELNET_IAC
		                  250,			# TELNET_SB
		                  31, 0, 200, 0, 0,	# TELOPT_NAWS
		                  255,			# TELNET_IAC
		                  240));		# TELNET_SE
		$telnet->telnetmode(1);

		my $telnetcmd = $cmd;

		$telnet->print("$telnetcmd");
		$telnet->getline;		# read out command line
		
		while( !$telnet->eof ) {
			my ($prematch, $match) = $telnet->waitfor(
				Match => '/\n/',
				Match => '/[\$%#>] {0,1}$/',
				Errmode => "return")
			or do {
			};
			if ($match =~ /[\$%#>] {0,1}$/) {
				$telnet->print("quit");
				$telnet->close;
				last;
			}
			my $str = $prematch.$match;
			if ( $str !~ /[\$%#>] {0,1}$/ ) {
				&udFormatString( $str );
			}
		}
	} elsif ( ( $protocol eq 'ssh' ) || ( $protocol eq 'ssh2' ) ) {
		eval "
			use utf8;
		";
		my %params = (
			'port' => 22,
			#'debug' => 1,
		);
		my $bAuthByKey = 0;
		my $szFileName = './.ssh/agent'.$arrAgent{ $iCurrentAgent }{ 'id' };
		if ( ( -e $szFileName ) && ( $arrAgent{ $iCurrentAgent }{ 'key' } ne '' ) ) {
			$bAuthByKey = 1;
			%params = (
				'port' => 22,
				'identity_files' => [$szFileName],
			)
		}
		my $sshVersion = 1;
		if ( $protocol eq 'ssh' ) {
			$params{ 'protocol' } = 1;
		} else {
			$sshVersion = 2;
			$params{ 'protocol' } = 2;
		}
		
		my $ssh = Net::SSH::Perl->new( $host, %params );
		if ( $sshVersion == 1 ) {
			$ssh->register_handler( 'stdout', sub {
				shift;
				my $packet = shift;
				my $str = $packet->get_str;
				&udFormatString( $str );
			} );
		} else {
			$ssh->register_handler( 'stdout', sub {
				shift;
				my $packet = shift;
				my $str = $packet->bytes;
				&udFormatString( $str );
			} );
		}
		utf8::encode( $login );
		if ( $bAuthByKey ) {
			$ssh->login( $login );
		} else {
			utf8::encode( $password );
			$ssh->login( $login, $password );
		}
		
		$ssh->cmd( "$command" );
	} elsif ( $protocol eq 'telnet' ) {
		eval "
			use Net::Telnet;
		";
		my $timeout = 30;
		my $telnet = new Net::Telnet;
		$telnet->errmode( sub { print "ERROR:" . join('|', @_) . "\n"; } );
		$telnet->timeout( $timeout );
		$telnet->option_callback( sub { return; } );
		$telnet->option_accept(Do => 31);		# TELOPT_NAWS
		$telnet->open(Host => $host,
		              Port => 23);

		if ($login ne "") {
			$telnet->waitfor('/(ogin|name|word):.*$/');
			$telnet->print("$login");
		}
		if ($password ne "") {
			$telnet->waitfor('/word:.*$/');
			$telnet->print("$password");
		}

		$telnet->waitfor(Match => '/.*[\$%>] {0,1}$/',
		                 Match => '/^[^#]*[\$%#>] {0,1}$/');

		$telnet->telnetmode(0);
		$telnet->put(pack("C9",
		                  255,			# TELNET_IAC
		                  250,			# TELNET_SB
		                  31, 0, 200, 0, 0,	# TELOPT_NAWS
		                  255,			# TELNET_IAC
		                  240));		# TELNET_SE
		$telnet->telnetmode(1);

		my $telnetcmd = $cmd;
		$telnetcmd .= " | no-more" if ( $arrAgent{ $iCurrentAgent }{ 'ostype' } eq 'Juniper' );

		$telnet->print("$telnetcmd");
		$telnet->getline;		# read out command line
		
		while( !$telnet->eof ) {
			my ($prematch, $match) = $telnet->waitfor(
				Match => '/\n/',
				Match => '/[\$%#>] {0,1}$/',
				Errmode => "return")
			or do {
			};
			if ($match =~ /[\$%#>] {0,1}$/) {
				$telnet->print("quit");
				$telnet->close;
				last;
			}
			my $str = $prematch.$match;
			if ( $str !~ /[\$%#>] {0,1}$/ ) {
				&udFormatString( $str );
			}
		}
	}
	print '</pre>';
}

sub print_form {
	print <<EOT;
<div class="label"><div class="l1"><div class="l2">Looking Glass</div></div></div>
<form method="get" action="$lgurl">
<div class="x9 query_form"><table>
	<tr class="top"><td class="l">&nbsp;</td><td class="c">&nbsp;</td><td class="r">&nbsp;</td></tr>
	<tr class="mid"><td class="l">&nbsp;</td><td class="c"><div class="x9_cont">
	
	<table>
		<tr>
			<td class="lbl">&nbsp;</td>
			<td class="inp">
EOT
	&udPrintQueries;
	print <<EOT;
			</td>
		</tr>
		<tr>
			<td class="lbl"><div>
EOT
	print 'Ip/Доменное имя:';
	print <<EOT;
			</div></td>
			<td class="inp"><div><input type="text" class="text" name="addr" value="$FORM{addr}"/></div></td>
		</tr>
		<tr>
			<td class="lbl"><div>
EOT
	print 'Региональный агент:';
	print <<EOT;
			</div></td>
			<td class="sel"><div>
EOT
	&udPrintAgent;
	print <<EOT;
			</div></td>
		</tr>
	</table>
	
	</div></td><td class="r">&nbsp;</td></tr>
	<tr class="bot"><td class="l">&nbsp;</td><td class="c">&nbsp;</td><td class="r">&nbsp;</td></tr>
</table></div>

<div class="x9 query_form"><table>
	<tr class="top"><td class="l">&nbsp;</td><td class="c"><span>Отфильтровать результаты по:</span></td><td class="r">&nbsp;</td></tr>
	<tr class="mid"><td class="l">&nbsp;</td><td class="c"><div class="x9_cont">
	
	<table>
		<tr>
			<td class="lbl">&nbsp;</td>
			<td class="inp">
EOT
	&udPrintFilter;
	print <<EOT;
	</table>
	
	</div></td><td class="r">&nbsp;</td></tr>
	<tr class="bot"><td class="l">&nbsp;</td><td class="c">&nbsp;</td><td class="r">&nbsp;</td></tr>
</table></div>

<div class="client_end"><table><tr>
	<td class="lbl"><div>&nbsp;</div></td>
	<td class="sbm2"><div><input id="submit_button" type="submit" class="sendquery" value="Выполнить"/></div></td>
</tr></table></div>
</form>
EOT
	print '<div id="loading" class="loading" style="display: none"><div class="l1"><div class="l2"><div class="clear">&nbsp;</div><img src="'.$root_relative.'/admin/skin/loading.gif" alt=""/><span>Ваш запрос обрабатывается</span><div class="clear">&nbsp;</div></div></div></div>';
	print <<EOT;
<script type="text/javascript">
EOT
	print "var sel = { disabled: ";
	if ( $FORM{ 'query' } eq "whois" ) {
		print "true";
	} else {
		print "false";
	}
	print ", disabled2: ";
	if ( ( $FORM{ 'query' } eq "" ) || ( $FORM{ 'query' } eq "bgp" ) ) {
		print "false";
	} else {
		print "true";
	}
	print " };\n</script>";
}

sub print_tail {
	print '<div class="got_result" style="display: none">&nbsp;</div>';
	print '</body></html>';
}

######## Portion of code is borrowed from NCSA WebMonitor "mail" code 

sub cgi_decode {
	my ($incoming) = @_;

	my %FORM;
	my $ref = "FORM";

	my @pairs = split(/&/, $incoming);
	
	#print 'Content-Type: text/html; charset=windows-1251';
	#print "\n\n";

	foreach (@pairs) {
		my ($name, $value) = split(/=/, $_);
		
		#print "name: $name, value[$value]<br/>";

		$name  =~ tr/+/ /;
		$value =~ tr/+/ /;
		#print "name: $name, value[$value]<br/>";
		$name = uri_unescape( $name );
		$value = uri_unescape( $value );
		#print "name: $name, value[$value]<br/>";
		#$name  =~ s/%([A-F0-9][A-F0-9])/pack("C", hex($1))/gie;
		#$value =~ s/%([A-F0-9][A-F0-9])/pack("C", hex($1))/gie;

		#### Strip out semicolons unless for special character
		#$value =~ s/;/$$/g;
		#$value =~ s/&(\S{1,6})$$/&\1;/g;
		#$value =~ s/$$/ /g;

		#$value =~ s/\|/ /g;
		#$value =~ s/^!/ /g; ## Allow exclamation points in sentences

		$FORM{$name} .= $value;
	}
	
	#exit;
	return (%FORM);
}

sub html_encode {
	($_) = @_;
	s|[\r\n]||g;
	s|&|&amp;|g;
	s|<|&lt;|g;
	s|>|&gt;|g;
	return $_;
}
