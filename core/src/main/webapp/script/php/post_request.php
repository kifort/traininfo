<?php
/**
 * Traininfo - Hungarian train timetable for Amazon Kindle eBook
 * @copyright Copyright (C) 2012-2022 Tamás Kifor
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/.
 *
 * If you have any question contact to Tamás Kifor via email: tamas@kifor.hu
 *
 * @author Tamás Kifor
 */
//This function is from http://www.jonasjohn.de/snippets/php/post-request.htm
function post_request($url, $data, $referer='') {

	// Convert the data array into URL Parameters like a=b&foo=bar etc.
	$data = http_build_query($data);

	// parse the given URL
	$url = parse_url($url);

	if ($url['scheme'] != 'http') {
		die('Error: Only HTTP request are supported !');
	}

	// extract host and path:
	$host = $url['host'];
	$path = $url['path'];

	// open a socket connection on port 80 - timeout: 30 sec
	$fp = fsockopen($host, 80, $errno, $errstr, 30);

	if ($fp){

		// send the request headers:
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");

		if ($referer != '')
			fputs($fp, "Referer: $referer\r\n");

		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ". strlen($data) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);

		$result = '';
		while(!feof($fp)) {
			// receive the results of the request
			$result .= fgets($fp, 128);
		}
	}
	else {
		return array(
				'status' => 'err',
				'error' => "$errstr ($errno)"
		);
	}

	// close the socket connection:
	fclose($fp);

	// split the result header from the content
	$result = explode("\r\n\r\n", $result, 2);

	$header = isset($result[0]) ? $result[0] : '';
	$content = isset($result[1]) ? $result[1] : '';

	// return as structured array:
	return array(
			'status' => 'ok',
			'header' => $header,
			'content' => $content
	);
}
?>