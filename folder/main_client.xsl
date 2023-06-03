<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="xml" encoding="UTF-8" omit-xml-declaration="yes"/>
	
	<xsl:template match="@*">
		<xsl:value-of select="name()"/>(<xsl:value-of select="."/>)
	</xsl:template>
	
	<xsl:template match="Error">
		<p><b><xsl:value-of select="@text"/></b></p>
	</xsl:template>
	
	<xsl:template match="Doc">
		<div class="bodyWrap"><div class="wrap">
			<div class="header">
				<div class="logo"><a href="{@logo_url}"><img src="{@logo_src}" alt="Rostelecom"/></a></div>
			</div>
			<xsl:apply-templates select="*"/>
		</div></div>
	</xsl:template>
	
	<xsl:template match="ClientMode">
		<xsl:for-each select="QueryForm[1]">
			<form action="{../@post_url}" method="get">
			<div class="x9 query_form"><table>
				<tr class="top"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
				<tr class="mid"><td class="l">&#160;</td><td class="c"><div class="x9_cont">
				
				<table>
					<tr>
						<td class="lbl">&#160;</td>
						<td class="inp"><div id="rad_list">
						
						<xsl:for-each select="*">
						<input id="rad{@name}" type="radio" name="query" value="{@name}">
						<xsl:if test="@sel">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
						</input>&#160;<label for="rad{@name}"><xsl:value-of select="@name"/></label><xsl:if test="position( ) != last( )">&#160;&#160;</xsl:if>
						</xsl:for-each>
						
						</div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Ip/Доменное имя:</div></td>
						<td class="inp"><div><input type="text" class="text" name="addr" value="{@addr}"/></div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Региональный агент:</div></td>
						<td class="sel"><div><select id="router" name="agent">
						<xsl:if test="@query = 'whois'">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</xsl:if>
						<xsl:for-each select="../Agent">
							<option value="{@agent_id}">
							<xsl:choose>
								<xsl:when test="string-length( @agent_title ) &gt; 0">
									<xsl:value-of select="@agent_title"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@agent_name"/>
								</xsl:otherwise>
							</xsl:choose>
							</option>
						</xsl:for-each>
						</select></div></td>
					</tr>
					<tr>
						<td class="lbl">&#160;</td>
						<td class="inp"><div>Отфильтровать результаты по:</div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Ip/Сеть/Regexp:</div></td>
						<td class="inp"><div>
						<input id="flt" type="text" class="text"  name="flt_inp" value="{@flt_inp}">
						<xsl:if test="(@query != '') and (@query != 'bgp')">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</xsl:if>
						</input>
						<input id="reg" type="checkbox" name="flt_reg" value="1">
						<xsl:choose>
							<xsl:when test="(@query != '') and (@query != 'bgp')">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:when>
							<xsl:when test="@flt_reg">
							<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:when>
						</xsl:choose>
						</input>&#160;<label for="reg">regexp</label>
						<span class="info">Если вы используете перл совместимое регулярное выражение, поставьте галочку regexp</span>
						</div></td>
					</tr>
					<tr>
						<td class="lbl"><div>Маска сети:</div></td>
						<td class="inp"><div><input id="msk" type="text" class="text" name="flt_msk" value="{@flt_msk}">
						<xsl:if test="(@query != '') and (@query != 'bgp')">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</xsl:if>
						</input></div></td>
					</tr>
					<tr>
						<td class="lbl">&#160;</td>
						<td class="sbm2"><div><input type="submit" class="sendquery" value="Выполнить"/></div></td>
					</tr>
				</table>
				
				</div></td><td class="r">&#160;</td></tr>
				<tr class="bot"><td class="l">&#160;</td><td class="c">&#160;</td><td class="r">&#160;</td></tr>
			</table></div>
			</form>
<script type="text/javascript">
	var sel = { disabled: <xsl:choose><xsl:when test="@query = 'whois'">true</xsl:when><xsl:otherwise>false</xsl:otherwise></xsl:choose>, disabled2: <xsl:choose><xsl:when test="(@query = '') or (@query = 'bgp')">false</xsl:when><xsl:otherwise>true</xsl:otherwise></xsl:choose>};
</script>
		</xsl:for-each>
	</xsl:template>
	
	
	<xsl:template match="ClientHelp">
<p><a href="{@root_relative}/lg.cgi">Looking Glass</a></p>
<h2>О Сервисе &amp;laquo;Looking Glass&amp;raquo;</h2>

<p>Сервис &amp;laquo;Looking Glass&amp;raquo; позволяет проверить доступность, получить whois-информацию, определить маршруты до интернет-ресурсов из автономной сети.</p>

<p>Сервис позволяет формировать следующие запросы:</p>
<ol>
	<li>ping</li>
	<li>traceroute</li>
	<li>bgp</li>
	<li>whois</li>
</ol>

<p>А также предоставляет средства для фильтрации результатов BGP-запросов по конкретной сети, паре сеть-маска или по регулярному выражению.</p>

<p><b>Ping</b> &amp;mdash; команда, позволяющая определить наличие и качество связи до заданного ресурса сети. Результатом работы комады ping будет список ответов (ICMP Echo-Reply). Причем время между отправкой запроса и получением ответа позволяет определить двусторонние задержки (RTT) по маршруту и частоту потери пакетов, то есть косвенно определить загруженность каналов передачи данных и промежуточных устройств.</p>

<p>В качестве запроса указывается IP-адрес или символьное имя хоста проверяемого ресурса. Запросы отправляются не с вашей локальной машины, а с  сервера-регионального агента системы Looking Glass, который задается в поле &amp;laquo;Региональный агент&amp;raquo;.</p>

<p><b>Traceroute</b> &amp;mdash; команда, позволяющая отследить пути прохождения пакетов по IP-сети из пункта А в пункт Б. Результатом работы traceroute будет список всех промежуточных узлов, находящихся между А и Б, а также время задержки до каждого промежуточного узла, т.е. время прохождения пакета туда и обратно.</p> 

<p>В качестве запроса указывается IP-адрес или символьное имя хоста. Трассировка маршрута происходит не с вашей локальной машины, а с сервера-регионального агента системы Looking Glass, который задается в поле &amp;laquo;Региональный агент&amp;raquo;.</p>

<p><b>BGP</b> &amp;mdash; команда, показывающая информацию протокола BGP — Border Gateway Protocol — основного протокола динамической маршрутизации в Интернете. BGP предназначен для обмена информацией о маршрутах не между отдельными маршрутизаторами, а между целыми автономными системами, и поэтому, помимо информации о маршрутах в сети, переносит также информацию о маршрутах на автономные системы. BGP не использует технические метрики, а осуществляет выбор наилучшего маршрута исходя из правил, принятых в сети.</p>

<p>В качестве запроса указывается IP-адрес или символьное имя хоста. Трассировка маршрута происходит не с вашей локальной машины, а с сервера-регионального агента системы Looking Glass, который задается в поле &amp;laquo;Региональный агент&amp;raquo;.</p>

<p><b>Whois</b> &amp;mdash; сервис для проверки доменов. Результатом выполнения запроса является информация о домене:  свободен домен или занят и, если занят, — информация о владельце домена.</p>

<p>В качестве запроса указывается доменное имя, IP-адрес ресурса или имя автономной системы.</p>

<h2>Как пользоваться сервисом &amp;laquo;Looking Glass&amp;raquo;</h2>

<img src="{@root_relative}/admin/skin/form_screen.png" alt="Форма"/><br/>
<p><small>Рис. 1. Интерфейс сервиса Looking Glass</small></p>

<p>Форма сервиса разделена на два блока: первый предоставляет поля для формирования запроса, второй — для условий фильтрации результатов запроса BGP.</p>

<h3>Формирование запроса</h3>
<ol>
<li>При помощи переключателей &amp;laquo;bgp&amp;raquo;, &amp;laquo;ping&amp;raquo;, &amp;laquo;trace&amp;raquo;, &amp;laquo;whois&amp;raquo; выберите нужный вам тип запроса.</li>

<li>
В поле &amp;laquo;Ip / Доменное имя&amp;raquo; введите Ip-адрес, доменное имя ресурса или имя автономной системы, по которым вы хотите получить инфомацию или проверку доступности которых вы хотите провести. 
<ul>
<li>Ip-адреса должны вводиться в формате Ipv4. Обрабатываются запросы только из блока публичных Ip-адресов. При введении адреса из блока адресов, зарезервированных для частных сетей, система выведет сообщение о невозможности обработки запроса.</li>
<li>Запросы whois возможны к автономным системам. В этом случае должно вводиться имя автономной системы. Обрабатываются запросы в диапазоне AS1 — AS64511, т.е. запросы к публичным именам. Запросы к зарезервированным именам из диапазона AS64512 — AS65535 не будут приняты, система выведет сообщение о невозможности обработки запроса.</li>
</ul>
</li>
<li>Из выпадающего списка поля &amp;laquo;Региональный агент&amp;raquo; выберите сервер / маршрутизатор, от которого будет трассироваться ваш запрос. Выбор регионального агента невозможен для запроса whois.</li>
</ol>
<h3>Фильтрация результатов запроса BGP</h3>
<p>В блоке &amp;laquo;Отфильтровать рузультаты по&amp;raquo; вы можете задать условия фильтрации результатов запроса BGP по следующим параметрам:</p>
<ol>
<li>IP-адресу</li> 
<li>конкретной сети</li> 
<li>паре сеть-маска — обратите внимание, что сеть и маска сети вводятся в разных полях</li> 
<li>perl-совместимому регулярному выражению. Если вы используете регулярное выражение, поставьте флажок &amp;laquo;regexp&amp;raquo;</li>
</ol>

<h3>Примеры фильтров</h3>
<ol>
<li>Сеть:<br/>
80.92.160.0
</li>
<li>Пара сеть-маска (должны быть введены в разные поля):<br/>
80.92.160.0 — сеть,<br/>
255.255.255.0 — маска сети.
</li>
<li>Регулярное выражение, которое в выдаче результатов оставляет только строки, содержащие IP-адреса:<br/>
(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)
</li>
</ol>
	</xsl:template>
	
</xsl:stylesheet>
