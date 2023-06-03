<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="xml" encoding="UTF-8" omit-xml-declaration="yes"/>
	
	<xsl:template match="@*">
		<xsl:value-of select="name()"/>(<xsl:value-of select="."/>)
	</xsl:template>
	
	<xsl:template match="Error">
		<p><b><xsl:value-of select="@text"/></b></p>
	</xsl:template>
	
	<xsl:template match="Pager">
		<div class="pager">
			<div class="clear">&#160;</div>
			<xsl:for-each select="PagerPrev">
				<xsl:choose>
					<xsl:when test="@page = 0">
						<span class="page_prev">Предыдущая</span>
					</xsl:when>
					<xsl:otherwise>
						<a class="page_prev" href="{../@url}page={@page}">Предыдущая</a>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:for-each select="PagerOption">
				<xsl:choose>
					<xsl:when test="@cur = 1">
						<span><xsl:value-of select="@page"/></span>
					</xsl:when>
					<xsl:otherwise>
						<a href="{../@url}page={@page}"><xsl:value-of select="@page"/></a>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:for-each select="PagerNext">
				<xsl:choose>
					<xsl:when test="@page = 0">
						<span class="page_next">Следующая</span>
					</xsl:when>
					<xsl:otherwise>
						<a class="page_next" href="{../@url}page={@page}">Следующая</a>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<div class="clear">&#160;</div>
		</div>
	</xsl:template>

	<xsl:template match="CMenu">
		<div class="menu_wrap"><table><tr>
			<td class="menu_lcol"><div class="menu"><!--table><tr-->
			<div class="clear">&#160;</div>
			<xsl:for-each select="*[ position( ) &lt; 3 ]">
				<!--td class="c1"-->
				<xsl:choose>
					<xsl:when test="@flags = 2"><!-- текущий элемент выбран -->
						<span>
						<xsl:attribute name="id">menu_item<xsl:value-of select="position( )"/></xsl:attribute>
						<xsl:value-of select="@title"/></span>
					</xsl:when>
					<xsl:when test="@flags = 3"><!-- выбран дочерний элемент текущего -->
						<a href="{@url}" class="cur">
						<xsl:attribute name="id">menu_item<xsl:value-of select="position( )"/></xsl:attribute>
						<xsl:value-of select="@title"/></a>
					</xsl:when>
					<xsl:otherwise>
						<a href="{@url}">
						<xsl:attribute name="id">menu_item<xsl:value-of select="position( )"/></xsl:attribute>
						<xsl:value-of select="@title"/></a>
					</xsl:otherwise>
				</xsl:choose>
				<!--/td-->
			</xsl:for-each>
			<div class="clear">&#160;</div>
			<!--/tr></table--></div></td>
			<td class="menu_rcol">
			<xsl:for-each select="*[ position( ) &gt; 2 ]">
				<xsl:choose>
					<xsl:when test="@flags = 2"><!-- текущий элемент выбран -->
						<span>
						<xsl:attribute name="id">menu_item<xsl:value-of select="position( ) + 2"/></xsl:attribute>
						<xsl:value-of select="@title"/></span>
					</xsl:when>
					<xsl:when test="@flags = 3"><!-- выбран дочерний элемент текущего -->
						<a href="{@url}" class="cur">
						<xsl:attribute name="id">menu_item<xsl:value-of select="position( ) + 2"/></xsl:attribute>
						<xsl:value-of select="@title"/></a>
					</xsl:when>
					<xsl:otherwise>
						<a href="{@url}">
						<xsl:attribute name="id">menu_item<xsl:value-of select="position( ) + 2"/></xsl:attribute>
						<xsl:value-of select="@title"/></a>
					</xsl:otherwise>
				</xsl:choose>
				&#160;
			</xsl:for-each>
			</td>
			</tr>
		</table></div>
	</xsl:template>
	
	<xsl:template match="LogFilter">
		<div class="log_filter"><form action="{../@base_url}/" method="get"><table><tr>
			<td class="col_dates">
				Дата:<br/>
				<input type="text" id="date1" name="d" value="{@d}"/>
			</td>
			<td class="col_ip">
				IP-адрес:<br/>
				<input type="text" name="ip" value="{@ip}"/>
			</td>
			<td class="col_type">
				Тип:<br/>
				<select name="t">
					<option value="">-- -- --</option>
					<option value="bgp"><xsl:if test="@t = 'bgp'">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>bgp</option>
					<option value="ping"><xsl:if test="@t = 'ping'">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>ping</option>
					<option value="trace"><xsl:if test="@t = 'trace'">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>trace</option>
					<option value="whois"><xsl:if test="@t = 'whois'">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>whois</option>
					<option value="illegal"><xsl:if test="@t = 'illegal'">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>illegal</option>
				</select>
			</td>
			<td class="col_agent">
				Агент:<br/>
				<input type="text" name="a" value="{@a}"/>
			</td>
			<td class="col_submit"><input type="submit" value="Выбрать"/></td>
		</tr></table></form></div>
	</xsl:template>
	
	<xsl:template match="Doc">
		<div class="bodyWrap"><div class="wrap">
			<div class="header">
				<div class="logo"><a href="{@logo_url}"><img src="{@logo_src}" alt="Rostelecom"/></a></div>
			</div>
			<xsl:apply-templates select="CMenu"/>
			<xsl:apply-templates select="*[name()!='CMenu']"/>
		</div></div>
	</xsl:template>
	
	<!-- Агенты -->
	<xsl:template match="AgentList">
		<div class="add_client"><a href="{@base_url}/+/">Добавить</a></div>
		<div class="list"><table>
			<tr><th class="col_name"><div>Имя</div></th><th class="col_ip"><div>IP-Адрес</div></th><th class="col_ostype"><div>Тип OS</div></th><th class="col_del">&#160;</th></tr>
		<xsl:for-each select="Agent">
			<tr>
			<td class="col_name"><div><a href="{../@base_url}/{@agent_id}/"><xsl:value-of select="@agent_name"/></a></div></td>
			<td class="col_ip"><div><xsl:value-of select="@agent_ip"/></div></td>
			<td class="col_ostype"><div><xsl:value-of select="@agent_ostype"/></div></td>
			<td class="col_del"><div><a href="{../@base_url}/{@agent_id}/del/">удалить</a></div></td>
			</tr>
		</xsl:for-each>
		</table></div>
	</xsl:template>
	
	<xsl:template match="AgentEdit">
		<h2><xsl:choose>
			<xsl:when test="@mode = 'add'">Добавление</xsl:when>
			<xsl:otherwise>Данные</xsl:otherwise>
		</xsl:choose> агента</h2>
		<xsl:apply-templates select="Error"/>
		<xsl:for-each select="Agent[1]">
		<div class="agent_edit"><form action="{../@base_url}/+/" method="post">
		<xsl:if test="../@mode = 'edit'">
			<xsl:attribute name="action"><xsl:value-of select="../@base_url"/>/<xsl:value-of select="@agent_id"/>/</xsl:attribute>
		</xsl:if>
		<div class="x9"><table>
			<tr class="top"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
			<tr class="mid"><td class="l">&#160;</td><td class="c"><div class="x9_cont">
			
			<table>
				<tr>
					<td class="lbl"><div>Имя</div></td>
					<td class="inp"><div><input type="text" class="text" name="agent_name" value="{@agent_name}"/></div></td>
				</tr>
				<tr>
					<td class="lbl"><div>IP-Адрес</div></td>
					<td class="inp"><div><input type="text" class="text" name="agent_ip" value="{@agent_ip}"/></div></td>
				</tr>
				<tr>
					<td class="lbl"><div>Логин</div></td>
					<td class="inp"><div><input type="text" class="text" name="agent_login" value="{@agent_login}"/></div></td>
				</tr>
				<tr>
					<td class="lbl"><div>Пароль</div></td>
					<td class="inp"><div><input type="text" class="text" name="agent_password" value="{@agent_password}"/></div></td>
				</tr>
				<tr>
					<td class="lbl"><div>Тип OS</div></td>
					<td class="sel"><div><select name="agent_ostype">
						<option value="Cisco">
						<xsl:if test="@agent_ostype = 'Cisco'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						Cisco</option>
						<option value="Juniper">
						<xsl:if test="@agent_ostype = 'Juniper'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						Juniper</option>
						<option value="Unix">
						<xsl:if test="@agent_ostype = 'Unix'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						Unix</option>
					</select></div></td>
				</tr>
				<tr>
                                        <td class="lbl"><div>Протокол</div></td>
                                        <td class="sel"><div><select name="agent_protocol">
                                                <option value="telnet">
                                                <xsl:if test="@agent_protocol = 'telnet'">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                telnet</option>
                                                <option value="ssh">
                                                <xsl:if test="@agent_protocol = 'ssh'">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                ssh</option>
                                                <option value="ssh2">
                                                <xsl:if test="@agent_protocol = 'ssh2'">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                ssh2</option>
                                        </select></div></td>
                                </tr>
                                <tr>
					<td class="lbl"><div>Файл ключа</div></td>
					<td class="txt"><div>
						<textarea name="agent_key"><xsl:value-of select="@agent_key"/></textarea>
						<span class="info">введите текст файла</span>
						<!--input type="text" class="text" name="agent_key_path" value="{@agent_key_path}"/-->
					</div></td>
				</tr>
				<tr>
					<td class="lbl"><div>Пароль для демона bgp</div></td>
					<td class="inp"><div><input type="text" class="text" name="agent_password2" value="{@agent_password2}"/></div></td>
				</tr>
				<tr>
					<td class="lbl"><div>Порт для демона bgp</div></td>
					<td class="inp"><div>
						<input type="text" class="text" name="agent_port" value="{@agent_port}"/>
						<span class="info">по умолчанию 23 - telnet</span>
					</div></td>
				</tr>
				<tr>
					<td class="lbl">&#160;</td>
					<td class="sbm2"><div><input type="submit" class="sendquery" value="Добавить">
					<xsl:if test="../@mode = 'edit'">
						<xsl:attribute name="value">Сохранить</xsl:attribute>
					</xsl:if>
					</input></div></td>
				</tr>
			</table>
			
			</div></td><td class="r">&#160;</td></tr>
			<tr class="bot"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
		</table></div>
		</form></div>
		</xsl:for-each>
	</xsl:template>
	
	
	<xsl:template match="StatList">
		<div class="cont_top"><xsl:apply-templates select="LogFilter"/></div>
		<div class="list"><table>
			<tr>
				<th class="col_datetime"><div>Дата</div></th>
				<th class="col_ip"><div>IP-адрес</div></th>
				<th><div>Агент</div></th>
				<th class="col_type"><div>Тип</div></th>
				<th><div>Адрес</div></th>
			</tr>
			<xsl:for-each select="Log">
			<tr>
				<td class="col_datetime"><div><xsl:value-of select="@log_cr_date"/></div></td>
				<td class="col_ip"><div><xsl:value-of select="@log_ip"/></div></td>
				<td><div><xsl:value-of select="@log_agent"/>&#160;</div></td>
				<td class="col_type"><div><xsl:value-of select="@log_type"/></div></td>
				<td><div><xsl:value-of select="@log_addr"/></div></td>
			</tr>
			</xsl:for-each>
		</table></div>
		<div class="client_end">&#160;</div>
		<xsl:apply-templates select="Pager"/>
	</xsl:template>
	
	<!-- Модуль входа в систему -->
	<xsl:template match="LoginForm">
		<div class="wrap">
			<div class="header">
				<div class="logo"><a href="{@logo_url}"><img src="{@logo_src}" alt="Rostelecom"/></a></div>
			</div>
			<xsl:apply-templates select="Error"/>
			<div class="login_form"><form action="{@post_url}" method="post"><div class="x9"><table>
				<tr class="top"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
				<tr class="mid"><td class="l">&#160;</td><td class="c"><div class="x9_cont">
					<table>
						<tr>
							<td class="lbl"><div>Логин:</div></td>
							<td class="inp"><div><input type="text" class="text" name="login" value=""/></div></td>
						</tr>
						<tr>
							<td class="lbl"><div>Пароль:</div></td>
							<td class="inp"><div><input type="password" class="text" name="password" value=""/></div></td>
						</tr>
						<tr>
							<td class="lbl">&#160;</td>
							<td class="sbm2"><div><input type="submit" class="sendquery" value="Войти"/></div></td>
						</tr>
					</table>
				</div></td><td class="r">&#160;</td></tr>
				<tr class="bot"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
			</table></div>
			</form></div>
		</div>
	</xsl:template>
	
	<!-- Модуль инсталляции -->
	<xsl:template match="Install1">
		<div class="conf">
		<h1>Системные настройки</h1>
		<xsl:apply-templates select="Error"/>
		<form action="{@post_url}" method="post">
		
		<div class="x9"><table>
			<tr class="top"><td class="l">&#160;</td>
				<td class="c"><span>Database account</span></td>
			<td class="r">&#160;</td></tr>
			<tr class="mid"><td class="l">&#160;</td><td class="c"><div class="x9_cont">
			<xsl:for-each select="CDbAccount[1]">
				<table>
					<tr>
						<td class="lbl"><div>Сервер:</div></td>
						<td class="inp"><div><input type="text" class="text" name="db[server]" value="{@server}"/></div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Имя пользователя:</div></td>
						<td class="inp"><div><input type="text" class="text" name="db[username]" value="{@username}" autocomplete="off"/></div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Пароль:</div></td>
						<td class="inp"><div><input type="text" class="text" name="db[password]" value="{@password}" autocomplete="off"/></div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Имя базы данных:</div></td>
						<td class="inp"><div><input type="text" class="text" name="db[database]" value="{@database}"/></div></td>
					</tr>
				</table>
			</xsl:for-each>
			</div></td><td class="r">&#160;</td></tr>
			<tr class="bot"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
		</table></div>
		
		<div class="x9"><table>
			<tr class="top"><td class="l">&#160;</td>
				<td class="c"><span>Superadmin account</span></td>
			<td class="r">&#160;</td></tr>
			<tr class="mid"><td class="l">&#160;</td><td class="c"><div class="x9_cont">
			<xsl:for-each select="Admin[1]">
				<table>
					<tr>
						<td class="lbl"><div>Логин:</div></td>
						<td class="inp"><div><input type="text" class="text" name="superadmin[admin_login]" value="{@admin_login}" autocomplete="off"/></div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Пароль:</div></td>
						<td class="inp"><div><input type="text" class="text" name="superadmin[admin_password]" value="" autocomplete="off"/></div></td>
					</tr>
				</table>
			</xsl:for-each>
			</div></td><td class="r">&#160;</td></tr>
			<tr class="bot"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
		</table></div>
		
		<div class="client_end"><table>
			<tr>
				<td class="lbl">&#160;</td>
				<td class="sbm2"><div><input type="submit" class="sendquery" value="Ok"/></div></td>
			</tr>
		</table></div>
		
		</form>
		</div>
	</xsl:template>
	
	
</xsl:stylesheet>
