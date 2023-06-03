<?php

	class Mail {
		var $sendto = array();
		var $acc = array();
		var $abcc = array();
		var $aattach = array();
		var $aattachName = array();
		var $xheaders = array();
		var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
		var $charset = "windows-1251";
		var $ctencoding = "8bit";
		var $strTo = '';
		var $body = '';
		var $contentType = "text/html";
		var $checkAddress = false;
	
		function Mail(){
			$this->boundary= "--" . md5( uniqid("myboundary") );
			/*
			if (c::$mailAdmin){
				if (c::$lang === 'ru' && (traslit(c::$siteTitle) !== c::$siteTitle || traslit(c::$mailAdmin) !== c::$mailAdmin)) $this->From("=?windows-1251?B?".base64_encode(c::$siteTitle)."?= <".c::$mailAdmin.">");
				else $this->From(c::$siteTitle." <".c::$mailAdmin.">");
				$this->To($this->xheaders['From']);
			}
			//*/
			//$this->body = getMailBody();
		}
	
		function Subject($subject = false){
			if ($subject) $this->xheaders['Subject'] = strtr( $subject, "\r\n" , "  " );
			return @$this->xheaders['Subject'];
		}
	
	
		function From($from){
			if (is_string($from)) {
				$this->xheaders['From'] = $from;
				ini_set('sendmail_from', $from);
			}
		}
	
		function To($to){
			if (is_array($to)) $this->sendto = $to;
			else $this->sendto = array($to);
			if($this->checkAddress == true) $this->CheckAdresses($this->sendto);
		}
	
		function ReplyTo($address){
			if (is_string($address)) $this->xheaders["Reply-To"] = $address;
		}
	
	
		/*
		 Cc()
		 set the CC headers ( carbon copy )
		 $cc : email address(es), accept both array and string
		 */
		function Cc( $cc )
		{
			if (is_array($cc) )
			$this->acc= $cc;
			else
			$this->acc[]= $cc;
	
			if ($this->checkAddress == true )
			$this->CheckAdresses( $this->acc );
		}
	
	
		/*	Bcc()
		 set the Bcc headers ( blank carbon copy ).
		 $bcc : email address(es), accept both array and string
		 */
		function Bcc( $bcc ){
			if (is_array($bcc)) $this->abcc = $bcc;
			else $this->abcc[] = $bcc;
	
			if ($this->checkAddress == true) $this->CheckAdresses($this->abcc);
		}
	
	
		/*	Body( text [, charset] )
		 *	set the body (message) of the mail
		 *	define the charset if the message contains extended characters (accents)
		 *	default to us-ascii
		 *	$mail->Body( "mйl en franзais avec des accents", "iso-8859-1" );
		 */
		function Body($body = false, $charset = -1){
			if (!$body) return $this->body;
			$this->body = $body;
			if ($charset !== -1) $this->charset = strtolower($charset);
		}
	
		function Priority($priority){
			if (!intval( $priority ) )
			return false;
	
			if (!isset( $this->priorities[$priority-1]) )
			return false;
	
			$this->xheaders["X-Priority"] = $this->priorities[$priority-1];
	
			return true;
	
		}
	
		function Attach( $filename, $filetype = "", $disposition = "attachment", $customFileName = '' ){
			if($filetype == "") $filetype = "application/x-unknown-content-type";
			$this->aattach[] = $filename;
			$this->aattachName[] = $customFileName;
			$this->actype[] = $filetype;
			$this->adispo[] = $disposition;
		}
	
		function BuildMail(){
			$this->headers = "";
			if (count($this->acc) > 0 )
			$this->xheaders['CC'] = implode( ", ", $this->acc );
	
			if (count($this->abcc) > 0 )
			$this->xheaders['BCC'] = implode( ", ", $this->abcc );
	
			if ($this->charset != "" ) {
				$this->xheaders["Mime-Version"] = "1.0";
				$this->xheaders["Content-Type"] = $this->contentType."; charset=$this->charset";
				$this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
			}
	
			// include attached files
			if (count( $this->aattach ) > 0 ) {
				$this->_build_attachement();
			} else {
				$this->fullBody = $this->body;
			}
	
			reset($this->xheaders);
			while( list( $hdr,$value ) = each( $this->xheaders )  ) {
				if ($hdr != "Subject") $this->headers .= "$hdr: $value\n";
			}
		}
	
	
		function asText(){
			$ret = '';
			$from = isset($this->xheaders['From']) && $this->xheaders['From'] ? $this->xheaders['From'] : 'anonim';
			$ret .= "<b>From</b>: {$from}<hr/>";
			$ret .= "<b>Subject</b>: {$this->Subject()}<hr/>";
			$ret .= "<b>Body</b>: ".$this->Body();
			return $ret;
		}
	
		/*
		 fornat and send the mail
		 @access public
		 */
		function Send(){
			if (!$this->charset) $this->charset = 'iso-8859-1';
			$this->BuildMail();
			$this->strTo = implode(", ", $this->sendto);
			/*
			$this->xheaders['Subject'] = str_replace('__SITE_NAME__', c::$siteTitle, $this->xheaders['Subject']);
			$this->fullBody = str_replace('__SITE_NAME__', c::$siteTitle, $this->fullBody);
			$this->fullBody = str_replace('__SITE_BASE__', SITE_BASE, $this->fullBody);
			//*/
			if (!$this->strTo){
				echo "<h3>Cant mail, send to email not set</h3><div class=alert_r>".$this->asText()."</div>";
				return false;
			}
			return mail($this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );
		}
	
	
		function ValidEmail($address){
			if(ereg( ".*<(.+)>", $address, $regs )) $address = $regs[1];
			return (ereg("^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$", $address));
		}
	
		function CheckAdresses( $aad){
			for($i=0;$i< count( $aad); $i++ ) {
				if (!$this->ValidEmail( $aad[$i]) ) {
					echo "Class Mail, method Mail : invalid address $aad[$i]";
					exit;
				}
			}
		}
	
	
		/*
		 check and encode attach file(s) . internal use only
		 @access private
		 */
	
		function _build_attachement(){
			$this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";
			$this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
			$this->fullBody .= "Content-Type: ".$this->contentType."; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";
			$sep = chr(13).chr(10);
			$ata = array();
			$k = 0;
	
			// for each attached file, do...
			for( $i=0; $i < count( $this->aattach); $i++){
				$filename = $this->aattach[$i];
				if ($this->aattachName[$i]) $basename = $this->aattachName[$i];
				else $basename = basename($filename);
				$ctype = $this->actype[$i];	// content-type
				$disposition = $this->adispo[$i];
	
				if (!file_exists( $filename)){
					echo "Class Mail, method attach : file $filename can't be found"; exit;
				}
				$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
				$ata[$k++] = $subhdr;
				// non encoded line length
				$linesz= filesize( $filename)+1;
				$fp= fopen( $filename, 'r' );
				$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
				fclose($fp);
			}
			$this->fullBody .= implode($sep, $ata);
		}
	
	}
	
	function DumbMail( $szSubject, $szFrom, $szTo, $szText ) {
		$m = new Mail( );
		if ( !empty( $szFrom ) ) {
			$m->From( $szFrom );
		}
		$m->To( $szTo );
		$m->Subject( $szSubject );
		$m->Body( $szText );
		$m->Send( );
		//@mail( $szTo, $szSubject, $szText );
	} // function DumbMail

?>