<?php
$con=new mysqli("localhost","root","a37gBSI09LahjF1a8103573Qs92Sn22n","eabdb");
//put in $_SESSION['user_name'] after Welcome
echo "
  <h1>Hi, Welcome <h1>
  <h2>Volunteer Schedule</h2>";
$query=
"SELECT role_table.role_name, temp2.program_name, temp2.date
	FROM (
		SELECT program_table.program_name, temp1.role_id, temp1.date
			FROM (
				SELECT program_relation_table.program_id, program_relation_table.date, temp.role_id
					FROM (
						SELECT signup_table.program_id, signup_table.role_id
							FROM `signup_table`
							JOIN `login_relation_table`
							ON signup_table.login_relation_id = login_relation_table.login_relation_id
							WHERE login_relation_table.user_id = 2
					) AS temp
					JOIN program_relation_table
					ON temp.program_id = program_relation_table.program_id
			) AS temp1
			JOIN program_table
			ON temp1.program_id = program_table.program_id
	) AS temp2
	JOIN role_table
	ON temp2.role_id = role_table.role_id";
$stmt=$con->prepare ($query);
//$stmt->bind_param ("s",$var1);
$stmt->bind_result ($ooga,$booga,$cooga);
$stmt->execute ();  
$i=0;
while ($stmt->fetch())
{
  echo "
    <h3>Volunteer Time $i</h3>
      <ul>
        <li>Role: $ooga</li>
        <li>Program: $booga</li>
        <li>Date: $cooga</li> 
      </ul>";
  $i++;
}
?>






