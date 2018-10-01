<?php
$servername = "localhost:3306";
$username = "admin";
$password = "admin";
$db = "employees";
// Create connection
$conn = new mysqli($servername, $username, $password, $db);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}else{
	echo '<h1>Connected Successfully !!</h1>';
}
/*echo 'Recieved vars ';
echo "id : " . $_POST["id"] . "<br>";
echo "lname : " . $_POST["lname"] . "<br>";
echo "dept : " . $_POST["dept"] . "<br>";
echo "dept_size : " . $_POST["dept_size"] . "<br>";
echo "gender : " . $_POST["gender"] . "<br>";*/

$dept_size=0;
$gender=0;
$sql="";

/*
 * SELECT dept_name, COUNT(*) AS dept_count FROM (SELECT employees.*, departments.dept_name FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no ) AS T GROUP BY dept_name;
 *
 *
 * SELECT dept_name, emp_count, m_count, f_count, ( m_count / f_count) as g_ratio FROM (SELECT dept_name, COUNT(case when gender='M' then 1 end) as m_count, count(case when gender='F' then 1 end) AS f_count, count(*) as emp_count FROM (SELECT employees.*, departments.dept_name, salaries.salary FROM employees, departments, dept_emp, salaries where salaries.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no AND employees.last_name='Famili') AS T GROUP BY dept_name order by emp_count desc ) AS T order by emp_count desc;
 *
 *
 *
 * Query 1 : done
 *
 *
 *
 * Query 2 : done
 *
 *
 * Query 3 : required department
 *SELECT emp_no, first_name, last_name, gender, birth_date, hire_date, dept_name FROM (SELECT employees.*, departments.dept_name, datediff(dept_emp.to_date, dept_emp.from_date) as tenure FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no and departments.dept_name='Sales' order by tenure desc) as TENURE;
 *
 * Query 4 : SELECT dept_name, emp_count, m_count, f_count, ( m_count / f_count) as g_ratio FROM (SELECT dept_name, COUNT(case when gender='M' then 1 end) as m_count, count(case when gender='F' then 1 end) AS f_count, count(*) as emp_count FROM (SELECT employees.*, departments.dept_name FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no) AS T GROUP BY dept_name order by emp_count desc ) AS T order by emp_count desc;
 *
 *
 * Query 5 : 
 *
 *
 *
 */

$sql = "";

if(!empty($_POST["q1"])){
	$sql = "SELECT employees.*, departments.dept_name FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no";
	if( $_POST["id"] || $_POST["lname"] || $_POST["dept"]){
		if($_POST["id"]){
			$sql = $sql . " AND employees.emp_no='". $_POST["id"] ."'";
			if($_POST["lname"]){
				$sql = $sql." AND employees.last_name='".$_POST["lname"]."'";
			}
			if($_POST["dept"]){
				$sql = $sql." AND departments.dept_name='".$_POST["dept"]."'";
			}
		}else if($_POST["lname"]){
			$sql = $sql . " AND employees.last_name='" . $_POST["lname"] . "'";
			if($_POST["dept"]){
				$sql = $sql." AND departments.dept_name='".$_POST["dept"]."'";
			}
		}else if($_POST["dept"]){
			$sql = $sql . " AND departments.dept_name='" . $_POST["dept"] . "'";
		}
	}
}else if(!empty($_POST["q2"])){
	$sql = "SELECT dept_name, COUNT(*) AS dept_count FROM (SELECT employees.*, departments.dept_name FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no ) AS T GROUP BY dept_name;";
}else if(!empty($_POST["q3"])){
	$sql = "SELECT emp_no, first_name, last_name, gender, birth_date, hire_date, dept_name FROM (SELECT employees.*, departments.dept_name, datediff(dept_emp.to_date, dept_emp.from_date) as tenure FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no and departments.dept_name='". $_POST["dept"] ."' order by tenure desc) as TENURE;";
}else if(!empty($_POST["q4"])){
	$sql = "SELECT dept_name, emp_count, m_count, f_count, ( m_count / f_count) as g_ratio FROM (SELECT dept_name, COUNT(case when gender='M' then 1 end) as m_count, count(case when gender='F' then 1 end) AS f_count, count(*) as emp_count FROM (SELECT employees.*, departments.dept_name FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no) AS T GROUP BY dept_name order by emp_count desc ) AS T order by emp_count desc;";
}else if(!empty($_POST["q5"])){
	$sql = "select dept_no, SUM(case when gender='F' then salary end)/SUM(case when gender='M' then salary end) as Ratio from (select dept_emp.dept_no, employees.gender, salaries.salary from dept_emp inner join employees on employees.emp_no = dept_emp.emp_no inner join salaries on salaries.emp_no = employees.emp_no inner join titles on employees.emp_no=titles.emp_no where titles.title='".$_POST["title"]."')temp where dept_no='".$_POST["dept_no"]."';";
}


/*echo "<hr>" ;
echo "generated query :<br>". $sql. "<br>" ;*/
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		if(!empty($_POST["q1"])){
			echo "<table>"; 
			echo "<tr><td>" . 'emp_no' . "</td><td>" . 'name' .  "</td><td>" . 'dept_name' . "</td><td>" . 'gender' . "</td></tr>";
			while($row = mysqli_fetch_array($result)){   
				echo "<tr><td>" . $row['emp_no'] . "</td><td>" . $row['first_name'] . $row['last_name'] .  "</td><td>" . $row['dept_name'] . "</td><td>" . $row['gender'] . "</td></tr>"; 
			}

			echo "</table>"; 
		}else if(!empty($_POST["q2"])){
			echo "<table>";
                        echo "<tr><td>" . 'dept_name' . "</td><td>" . 'emp_count' . "</td></tr>";
                        while($row = mysqli_fetch_array($result)){
                                echo "<tr><td>" . $row['dept_name'] . "</td><td>" . $row['dept_count'] . "</td></tr>";
                        }
                        echo "</table>";
		}else if(!empty($_POST["q3"])){
			echo "<table>";
                        echo "<tr><td>" . 'emp_no' . "</td><td>" . 'name' .  "</td><td>" . 'dept_name' . "</td><td>" . 'gender' . "</td></tr>";
                        while($row = mysqli_fetch_array($result)){
                                echo "<tr><td>" . $row['emp_no'] . "</td><td>" . $row['first_name'] . $row['last_name'] .  "</td><td>" . $row['dept_name'] . "</td><td>" . $row['gender'] . "</td></tr>";
                        }

                        echo "</table>";
		}else if(!empty($_POST["q4"])){
			echo "<table>";
                        echo "<tr><td>" . 'dept_name' . "</td><td>" . 'gender_ratio' . "</td></tr>";
                        while($row = mysqli_fetch_array($result)){
                                echo "<tr><td>" . $row['dept_name'] . "</td><td>" . $row['g_ratio'] . "</td></tr>";
                        }
                        echo "</table>";
		}else if(!empty($_POST["q5"])){
			if(!empty($row['Ratio'])){
				echo 'Pay ratio : ' .  $row['Ratio'];
			}else{
				echo "No such employee in this department.";
			}
		}
	}
}


/* if($_POST["gender"]=="Y"){
	$gender = 1;
}

if($_POST["dept_size"]=="Y"){
	$dept_size=1;
	$sql = "SELECT dept_name, emp_count, m_count, f_count, ( m_count / f_count) as g_ratio FROM (SELECT dept_name, COUNT(case when gender='M' then 1 end) as m_count, count(case when gender='F' then 1 end) AS f_count, count(*) as emp_count FROM (SELECT employees.*, departments.dept_name FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no"; 
	$later = ") AS T GROUP BY dept_name order by emp_count desc ) AS T order by emp_count desc;";
}else{
	$sql = "SELECT employees.*, departments.dept_name FROM employees, departments, dept_emp where dept_emp.dept_no=departments.dept_no AND dept_emp.emp_no=employees.emp_no AND dept_emp.dept_no=departments.dept_no";
}

+--------------------+
| title              |
+--------------------+
| Senior Engineer    |
| Staff              |
| Engineer           |
| Senior Staff       |
| Assistant Engineer |
| Technique Leader   |
| Manager            |
+--------------------+
if( $_POST["id"] || $_POST["lname"] || $_POST["dept"]){
	if($_POST["id"]){
		$sql = $sql . " AND employees.emp_no='". $_POST["id"] ."'";
		if($_POST["lname"]){
			$sql = $sql." AND employees.last_name='".$_POST["lname"]."'";
		}
		if($_POST["dept"]){
			$sql = $sql." AND departments.dept_name='".$_POST["dept"]."'";
		}
	}else if($_POST["lname"]){
		$sql = $sql . " AND employees.last_name='" . $_POST["lname"] . "'";
		if($_POST["dept"]){
			$sql = $sql." AND departments.dept_name='".$_POST["dept"]."'";
		}
	}else if($_POST["dept"]){
			$sql = $sql . " AND departments.dept_name='" . $_POST["dept"] . "'";
		}
}

if($dept_size == 1){
	$sql = $sql . $later ;
}

		echo "<hr>" ;
	echo "generated query :<br>". $sql. "<br>" ;
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			if($dept_size == 1){
				if($gender == 1){
					echo "[ department : " .  $row["dept_name"] . " ]" . " [ employee_count : " . $row["emp_count"] . " ] [ gender_ratio : " . $row["g_ratio"] .  " ] <br>";
				}else{
					echo "[ department : " .  $row["dept_name"] . " ]" . " [ employee_count : " . $row["emp_count"] . " ] <br>";
				}
			}else if($gender == 1){
				echo "[id: " . $row["emp_no"]. " ] [ Name: " . $row["first_name"]. " " . $row["last_name"]. "]  [ Dept : " . $row["dept_name"] . " ] " . "[ gender : " . $row["g_ratio"] . " ]" . "<br>";
			}else{
				echo "[id: " . $row["emp_no"]. " ] [ Name: " . $row["first_name"]. " " . $row["last_name"]. "]  [ Dept : " . $row["dept_name"] . " ] " . "<br>";
			}
}
} else {
	echo "0 results";

}*/

?>
