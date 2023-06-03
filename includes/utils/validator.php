<?php
	/**
	 * 	Валидатор различных элементов
	 */
	class CValidator {
		
		/**
		 * 	Проверяет логин
		 * 	@param $szLogin string логин
		 * 	@param $iMinLen int минимальная длина логина
		 * 	@param $iMaxLen int максимальная длина логина
		 * 	@return bool
		 */
		public static function Login( $szLogin, $iMinLen = 1, $iMaxLen = 20 ) {
			$szRegExp = '/[^0-9a-zA-Z]/sU';
			return ( bool) !( preg_match( $szRegExp, $szLogin ) || ( strlen( $szLogin ) > $iMaxLen ) || ( strlen( $szLogin ) < $iMinLen ) );
		} // function Login
		
		/**
		 * 	Проверяет доменное имя
		 * 	@param $szDomain string доменное имя
		 * 	@param $bUseFilter bool использовать фильтр ( true ) или регулярку ( false )
		 * 	@param $bIgnoreDot bool игнорировать ли точку в конце имени
		 * 	@return bool
		 */
		public static function DomainName( $szDomain, $bUseFilter = false, $bIgnoreDot = false ) {
			// либо выдергиваем часть из регулярки для мыла, либо строим фэйк мыло
			//$szRegExp = '/^((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
			if ( $bIgnoreDot ) {
				$szDomain = preg_replace( '/\.$/sU', '', $szDomain );
			}
			return CValidator::Email( "test@".$szDomain, $bUseFilter );
		} // function DomainName
		
		/**
		 * 	Проверяет IP адрес на верность
		 * 	@param $szIp string строка ip адрес
		 * 	@param $bIpv4 bool адрес формата IPv4 ( true ), IPv6 - false
		 * 	@return bool
		 */
		public static function IpAddress( $szIp, $bIPv4 = true ) {
			return filter_var( $szIp, FILTER_VALIDATE_IP, ( $bIPv4 ? FILTER_FLAG_IPV4 : FILTER_FLAG_IPV6 ) | /*FILTER_FLAG_NO_PRIV_RANGE |*/ FILTER_FLAG_NO_RES_RANGE );
		} // function IpAddress
		
		/**
		 * 	Проверяет правильность e-mail адреса
		 * 	@param $szEmail string строка e-mail
		 * 	@param $bUseFilter bool использовать фильтр ( true ) или регулярку ( false )
		 * 	@return bool
		 */
		public static function Email( $szEmail, $bUseFilter = true ) {
			// регулярка проверки email
			$szRegExp = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
			if ( $bUseFilter ) {
				return ( bool ) filter_var( $szEmail, FILTER_VALIDATE_EMAIL );
			} else {
				return ( bool ) preg_match( $szRegExp, $szEmail );
			}
		} // function Email
		
		/**
		 * 	Проверяет телефонный номер
		 * 	@param $szPhone string строка, которая должна быть телефоном
		 */
		public static function Phone( $szPhone ) {
			if ( preg_match( '/[^0-9\s+]/sU', $szPhone ) ) {
				return false;
			}
			return true;
		} // function Phone
		
		/**
		 * 	Проверяет, что текст содержит только английские буквы
		 * 	@param $szText string строка, которая должна содержать только латинские буквы и пунктуацию
		 */
		public static function EngOnly( $szText ) {
			$szRegExp = '/[^a-zA-Z0-9;,\\\.\/\-"\'\s]/sU';
			return ( bool ) !preg_match( $szRegExp, $szText );
		} // funciton EngOnly
		
	} // class CValidator

?>