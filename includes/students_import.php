<?php
function csv_to_array($filename='', $delimiter=',', $level)
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            
						//echo "<br>Name: ";
						$nameString =	$row[0];					
						$nameArray = explode(",", $nameString);
	
						//echo "<br>Name Array:";
						//print_r($nameArray);
						
						//print_r($nameString);											
						//echo "<br>Row <br>";
						///print_r($row);
  					  
						$rowData = array();
						$rowData[]= $row[1] ;//id
						$rowData[]= $nameArray[1];//name	
						$rowData[]= $nameArray[0];//lastname													
						$rowData[] =strtolower(substr(trim($nameArray[1]),0,1).trim($nameArray[0]));//user_id
						$num = (string)$row[1];
						$rowData[] =substr($num,strlen($num)-4, strlen($num));//password
								
						$rowData[] = $level; 
				//	print_r($rowData);
						insertStudent($rowData);
						
				//echo "<br> Test <br>".$nameArray[1]."Test <br>". substr(trim($nameArray[1]),0,1)." <br>".trim(substr($nameArray[1],0,1));
				//print_r($nameArray);
						$data[]= $rowData;
	        }
        fclose($handle);
    }
    return $data;
}

function insertStudent($row){
	$user_name = $row[3];
	$password = $row[4];
	

	echo "Password for ".$user_name ."is: ".$password;
	
	
	$user_id = username_exists( $user_name );
	if(!$user_id){
		wp_create_user( $user_name, $password);
	}
	else{
		echo "<br> User Exists: ".$user_name;
	}
}




?>