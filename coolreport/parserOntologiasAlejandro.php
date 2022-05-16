$owl = file_get_contents("C:/Users/Alfonso/workspace/PhD_MDSS_GUI/ont/signs.owl");

		$array = explode("<rdf:Description", $owl);
		array_splice($array, 0, 2);
		
		$dictionary = [];
		$string_disorders = ""; 
		foreach ($array as $element)
		{
			$id_init = strpos($element, '#')+1;
			$id_end = strpos($element, '">');			

			$position = strpos($element, '<rdfs:label>');
			if ($position !== false)
			{
				$init = strpos(substr($element, $position), '>')+1;
				$end = strpos(substr($element, $position), '</rdfs:label>');

				$id = substr($element, $id_init, $id_end-$id_init);
				$label = substr(substr($element, $position), $init, $end-$init);
				$string_disorders .= $label . ',' . $id . PHP_EOL;

				$dictionary[] = [$id, $label];
			}
		}

		file_put_contents('findings.txt', $string_disorders);

		dd($dictionary);

		$signs = file_get_contents("C:/diseases_es.txt");
		$array_signs = explode("\r\n", $signs);

		$json = [];
		foreach ($array_signs as $sign)
		{
			$elements = explode(", ", $sign);
			$json[$elements[1]] = $elements[0];
		}

		file_put_contents('diseases.json', json_encode($json));
		dd("fin");

		$signs = file_get_contents("C:/diseases_doctors_es.txt");
		$array_signs = explode("\r\n", $signs);

		$json = [];
		foreach ($array_signs as $sign)
		{
			$elements = explode(", ", $sign);
			$json[$elements[1]] = array("name" => $elements[0], "specialty" => $elements[2], "doctor" => $elements[3] .', '. $elements[4]);
		}

		file_put_contents('diseases.json', json_encode($json));
		dd("fin");