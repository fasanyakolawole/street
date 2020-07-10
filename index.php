<?php

class HomeOwnerFileParser {

    public function process_file($file_name) {

        $file = fopen($file_name,"r");

        while(! feof($file)){
            $line = fgetcsv($file, 1000, ",");
            $input = $line[0];
            
            $text = str_replace(" and ", "&", $input);
            $text = str_replace(".", "", $text);

            $persons = $this->extract_persons($text);

            $this->print_result($input, $persons);
           
        }
    }

    private function extract_persons($text) {

        $output = [];
        $name_parts = explode("&", $text);
        $words_in_line = explode(" ", $text);

        $last_name = $words_in_line[sizeof($words_in_line) - 1];

        foreach($name_parts as $name_part) {
            $name_part = trim($name_part);
            $sub_name_parts = explode(" ", $name_part);

            if(sizeof($sub_name_parts) == 1) {
                if(sizeof($words_in_line) == 1) {
                    $person = $this->create_person(null, $name_part, null, null);
                }
                else {
                    $person = $this->create_person($name_part, null, $last_name, null);
                }
            }

            else if (sizeof($sub_name_parts) == 2) {
                $person = $this->create_person($sub_name_parts[0], null, $last_name, null);    
            }

            if(sizeof($sub_name_parts) == 3) {
                $person['title'] = $sub_name_parts[0];
                $person['last_name'] = $sub_name_parts[2];

                if(strlen($sub_name_parts[1]) == 1) {
                    $person['initial'] = $sub_name_parts[1];
                }
                else {
                    $person['first_name'] = $sub_name_parts[1];   
                }

            }

            $output[] = $person;
        }

        return $output;
    }

    private function print_value($property, $value, $skip_comma = false) {
        $prepared_value = ($value == null) ? 'null' : '\'' . $value . '\'';
        echo '$person[\'' . $property . '\'] => ' . $prepared_value . ($skip_comma ? '' : ',') . '</br>';
    }

    private function create_person($title, $first_name, $last_name, $initial) {
        return array("title"=>$title, "first_name"=>$first_name, "last_name" => $last_name, "initial" => $initial);
    }

    private function print_result($input, $persons) {

        $properties = ["title", "first_name", "last_name", "initial"];

        echo "Input<br/>";
        echo '`"' . $input . '"`<br/><br/>';

        echo '<br/>';

        $i = 0;
        $j = 0;
        foreach($persons as $person) {

            $i++;
            foreach($properties as $property) {
                $j++;

                $skip_comma = ($i == sizeof($persons) && $j == sizeof($properties));
                $this->print_value($property, isset($person[$property]) ? $person[$property] : null, $skip_comma);
            }
        }

        echo '<br/><br/>';

    }
}

$fileParser = new HomeOwnerFileParser();
$fileParser->process_file("test.csv");