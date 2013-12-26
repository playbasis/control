<!DOCTYPE html>
<html>
	<head>
		<title>Playbasis Reset Your Password</title>
		<style>
			body{
				font-family: Arial;
			}
			img {
				margin: 0 auto;
			}

			h1, h2{
				text-align: center;
			}

			a{
				font-size: 20px;	
			}
			#container{
				width:500px;
				margin: 0 auto;
			}
		</style>
	</head>
	<body>
		<div id="container">
			<img src="http://www.playbasis.com/assets/img/playbasis.png" alt="Playbasis" title="Playbasis"/>
			<h1>Hello {firstname} {lastname}!</h1>
			<br/>
			<br/>
			<h2>Please click the link below in order to use your Playbasis account:</h2>
			<br/>
			{url}
			<br>
			Your username is: {username}
			<br/>
			Your password is: {password}
		</div>
	</body>
</html>