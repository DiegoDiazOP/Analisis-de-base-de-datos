	<?php 

		echo"
			<html>
				<head>
					<link rel='stylesheet' type='text/css' href='style.css'>
				</head>
				<body>
					<section>
					<h1>Statistics</h1>
		";

		/*Conexion SQL*/
		$link=mysqli_connect("localhost","root","");
		if($link){
			
			$base=$_POST["base"];
			$tabla=$_POST["tabla"];
			$variable=$_POST["variable"];
			$tipo=$_POST["tipo"];
		
			/*Seleccion de la base de datos*/
			$db=mysqli_select_db($link,"$base");
			if($db){
				/*Almacenando en un array*/
				$X=array(0);
				$i=0;
				$sql="SELECT * FROM $tabla";   //cambiar
				$result=mysqli_query($link,$sql);
				
				while($fila=mysqli_fetch_array($result)){			  
					$X[$i]=$fila[$variable];
					$i=$i+1;
				}

				$n=count($X);
				sort($X);
				
				/*Funcion para el promedio*/
				function prom($x){
					$n=count($x);
					$s=0;
					for($i=0;$i<$n;$i++){
						$s=$s+$x[$i];
					}
					return $s/$n;
				}

				/*Funcion para la mediana*/
				function mediana($x){
					$n=count($x);
					$med=0;
					if($n%2==0){
					   $med=($x[$n/2-1]+$x[$n/2])/2;
					}
					else{
						$med=$x[($n+1)/2-1];
					}
					return $med;
				}
				
				/*Funcion para la varianza*/
				function vari($x){
					$n=count($x);
					$s1=0;
					for($i=0;$i<$n;$i++){
						$s1=$s1+pow($x[$i],2);
					}
					$s2=($s1-$n*pow(prom($x),2))/($n-1);
					return $s2;
				}
				
				/*Tabla de frecuencias de los valores discretos*/
				function resumen1($x){ 
					sort($x);
					$x[count($x)]="null";
					/*Posiciones de los valores unicos*/
					$pos=array(0);
					/*Valores discretos sin repetir*/
					$val=array(0);
					$t=0;
					for($i=0;$i<count($x)-1;$i++){
						if($x[$i]!=$x[$i+1]){
							$pos[$t]=$i;
							$t++;
							}
					}
					$c=0;
					foreach($pos as $valor){
						$val[$c]=$x[$valor];
						$c++;
					}
					/*Frecuencias de valores discretos*/
					$frec=array(0);
					for($i=0;$i<count($pos);$i++){
						$c=0;
						for($j=0;$j<count($x)-1;$j++){
							if($val[$i]==$x[$j]){
								$c++;
								$frec[$i]=$c;
							}
						}
					}
					
					$c=count($val);
					$fr=array(0);
					for($i=0;$i<$c;$i++){
						$fr[$i]=$frec[$i]*100/($c*prom($frec));
					}
					
					echo"												
						<table class='tabla'>
							<tr>
								<td><b>Valores de X</b></td>
								<td><b>Frecuencias absolutas</b></td>
								<td><b>Frecuencias relativas</b></td>
							</tr>
						</table>
					";
					
					for($i=0;$i<count($val);$i++){
						echo"
							<table class='tabla'>
								<tr>
									<td>".round($val[$i],3)."</td>
									<td>".round($frec[$i],3)."</td>
									<td>".round($fr[$i],3)."%</td>
								</tr>
							</table>
						";
					}
				}
				
				/*Tabla de frecuencias de los valores continuos*/
				function resumen2($x){
					sort($x);
					/*Regla de Sturges*/
					$r=$x[count($x)-1]-$x[0];
					$k=1+3.332*log10(count($x));
					if($k-floor($k)>=0.1){$k=floor($k+1);}
					else{$k=floor($k);}
					$a=$r/$k;
					$error=$r-$a*$k;
					/*Intervalos*/
					$int1=array(0);
					$int1[0]=$x[0];
					for($i=1;$i<$k+1;$i++){
					$int1[$i]=$int1[$i-1]+$a;
					}
					$int1[0]=$int1[0]-$error;
					$int1[count($int1)-1]=$int1[count($int1)-1]+$error;
					/*Marcas de clase*/
					$marcas=array(0);
					for($i=0;$i<count($int1)-1;$i++){
						$marcas[$i]=($int1[$i]+$int1[$i+1])/2;
					}
					/*Frecuencias absolutas*/
					$frec=array(0);
					for($i=0;$i<$k;$i++){
						$c=0;
						for($j=0;$j<count($x);$j++){
							if($x[$j]>=$int1[$i]&$x[$j]<$int1[$i+1]){
								$c++;
							}
						}
						$frec[$i]=$c;
					}
					$c=count($marcas);
					/*Frecuencias relativas*/
					$fr=array(0);
					
					for($i=0;$i<$c;$i++){
						$fr[$i]=$frec[$i]*100/($c*prom($frec));
					}
					
					echo"
						<table class='tabla'>
							<tr>
								<td><b>Intervalos</b></td>
								<td><b>Marcas de clase</b></td>
								<td><b>Frecuencias<br>absolutas</b></td>
								<td><b>Frecuencias<br>relativas</b></td>
							</tr>
						</table>
						";
				
					for($i=0;$i<count($marcas);$i++){
						echo"
							<table class='tabla'>
								<tr>
									<td>".round($int1[$i],3).", ".round($int1[$i+1],3)."</td>
									<td>".round($marcas[$i],3)."</td>
									<td>".round($frec[$i],3)."</td>
									<td>".round($fr[$i],3)."%</td>
								</tr>
							</table>
						";
					}
				}
				
				/*Mostrar resultados*/
				/*Moda*/
				sort($X);
				$X[count($X)]="null";
				/*Posiciones de los valores unicos*/
				$pos=array(0);
				/*Valores discretos sin repetir*/
				$val=array(0);
				$t=0;
				for($i=0;$i<count($X)-1;$i++){
					if($X[$i]!=$X[$i+1]){
						$pos[$t]=$i;
						$t++;
						}
				}
				$c=0;
				foreach($pos as $valor){
					$val[$c]=$X[$valor];
					$c++;
				}
				/*Frecuencias de valores discretos*/
				$frec=array(0);
				for($i=0;$i<count($pos);$i++){
					$c=0;
					for($j=0;$j<count($X)-1;$j++){
						if($val[$i]==$X[$j]){
							$c++;
							$frec[$i]=$c;
						}
					}
				}
				$frecm=0;
				for($i=0;$i<count($frec);$i++){
					if($frec[$i]>$frecm){
						$frecm=$frec[$i];
						$pmod=$i;
					}
				}
				$mod=$val[$pmod];
				unset($X[count($X)-1]);
				/*****/
				echo"
					<br>
					<table class='tabla'>
						<tr>
							<td><b>Minimo</b></td>
							<td><b>Maximo</b></td>
						</tr>
						<tr>
							<td>".$X[0]."</td>
							<td>".$X[$n-1]."</td>
						</tr>
					</table>
					<br>
					<table class='tabla'>
						<tr>	
							<td><b>Promedio</b></td>
							<td><b>Mediana</b></td>
							<td><b>Moda</b></td>
						</tr>
						<tr>
							<td>".round(prom($X),3)."</td>
							<td>".round(mediana($X),3)."</td>
							<td>".round($mod,3)."</td>
						</tr>
					</table>
					<br>
					<table class='tabla'>
						<tr>
							<td><b>Rango</b></td>
							<td><b>Variancia</b></td>
							<td><b>Desviación estandar</b></td>
							<td><b>Coeficiente de variación</b></td>
						</tr>
						<tr>
							<td>".($X[$n-1]-$X[0])."</td>
							<td>".round(vari($X),3)."</td>
							<td>".round(sqrt(vari($X)),3)."</td>
							<td>".round((sqrt(vari($X))*100/prom($X)),3)."%</td>
						</tr>
					</table>
					<br>
				";

				if(1==$tipo){
					resumen1($X);
				}
				else{
					resumen2($X);
				}

				echo"
								<br>
								<footer>
									<a href='http://localhost/statistics.html'>Regresar</a>
								</footer>	
							</section>
						</body>	
					</html>
				";

			}
			
			else{
				echo'<script>alert("No hay variables almacenadas.");window.history.go(-1);</script>';
			}
		}
		
		else{
			echo'<script>alert("No se establecio conexion.");window.history.go(-1);</script>';
		}	
	?>