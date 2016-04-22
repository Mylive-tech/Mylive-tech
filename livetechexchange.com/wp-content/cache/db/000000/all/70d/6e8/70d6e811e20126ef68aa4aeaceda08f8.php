^˜V<?php exit; ?>a:6:{s:10:"last_error";s:0:"";s:10:"last_query";s:286:"
			SELECT instances.raw_url
			FROM wp_blc_instances AS instances JOIN wp_blc_links AS links 
				ON instances.link_id = links.link_id
			WHERE 
				instances.container_type = 'page'
				AND instances.container_id = 12
				AND links.broken = 1
				AND parser_type = 'link' 
		";s:11:"last_result";a:0:{}s:8:"col_info";a:1:{i:0;O:8:"stdClass":13:{s:4:"name";s:7:"raw_url";s:7:"orgname";s:7:"raw_url";s:5:"table";s:9:"instances";s:8:"orgtable";s:16:"wp_blc_instances";s:3:"def";s:0:"";s:2:"db";s:19:"mylivete_exchangev3";s:7:"catalog";s:3:"def";s:10:"max_length";i:0;s:6:"length";i:262140;s:9:"charsetnr";i:45;s:5:"flags";i:4113;s:4:"type";i:252;s:8:"decimals";i:0;}}s:8:"num_rows";i:0;s:10:"return_val";i:0;}