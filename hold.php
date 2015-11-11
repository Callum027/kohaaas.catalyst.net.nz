<!doctype html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<title>Koha</title>
	<link rel="SHORTCUT ICON" href="favicon.ico" />
	<link rel="icon" href="favicon.ico" type="image/ico" />
	<meta name="keywords" content="wellington,koha, service, catalyst cloud, catalyst">
	<meta name="description" content="This webpage is a front for a site testing registration and deployment of sites on the Koha as a Service system" />
	<meta name="author" content="Webpage:Francesca Moore; Koha as a Service: Callum Dickinson" />
	<meta name="copyright" content="Copyright 2015 Callum Dickinson and Francesca Moore " />
	<meta name="robots"content= "noindex, nofollow" />
	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" href="css/mobile.css" media="only all and (max-width: 480px)" />
	<link rel="stylesheet" href="css/tablet.css" media="only all and (min-width: 481px)" />
	<link rel="stylesheet" href="css/styles.css" media="only all and (min-width: 960px)" />
	<link rel="stylesheet" href="css/default.css" media="only all and (min-width: 480px") /> <!---works in conjuction with mobile and tablet style sheets--->
        <script src="//code.jquery.com/jquery-2.1.4.min.js" /> 
        <script type="text/javascript">
          var BASE_URL = 'http://koha-sandbox.catalyst.net.nz/';
        </script>
	<!-- the following script operates for older browsers incl. mobile -->
	<!--[if lt IE 9 & !IEMobile]>
			<link rel="stylesheet" href="css/mobile.css" />
			<link rel="stylesheet" href="css/laptop.css" />
		<![endif]-->

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]--> 
</head>

<body>
	<div id="container"> <!--this holds everything-->
	  <header>
      <img src="assets/koha.png" alt="koha logo"/>
	   <hgroup>
		<h1><i>Koha as a Service</i></h1>
		<h2>You are here: hold</h2>
	   </hgroup>
	  </header>

	  <nav class="clearfix">
		<ul>
		  <li><a href="http://koha-community.org">Koha main webpage</a></li>
		  <li><a href="http://dashboard.koha-community.org/">Koha Dashboard</a></li>
            
          <li><a href="http://wiki.koha-community.org/wiki/Main_Page">Koha Wiki</a></li>
            
         <li><a href="https://www.catalyst.net.nz/content/homepage-products">Catalyst IT</a></li>
		</ul>
	  </nav>
	  


	<article>
		<a  class="mobile-only" href="#jumpbottom" >To Navigation</a>
		<a class="mobile-only" name="jumptop"></a> <!---these links are for mobile only, they allow easy navigation whilst on a mobile device.--->

        <div id="headline">
          <p>Your Koha instance is being built.</p>
          <p id="small"><i>This may take a few minutes...</i></p>
        </div>
	
        <div id="db-status"></div>	
        <div id="memcached-status"></div>
        <div id="zebra-status"></div>
        <div id="koha-status"></div>
        <div id="proxy-status"></div>

        <script type="text/javascript">
          function component_ready(name, id)
          {
            return $.getJSON(BASE_URL + 'api/status/' + name + '/' + id).ready;
          }

          component_ready('db', id);
          component_ready('memcached', id);
          component_ready('zebra', id);
          component_ready('koha', id);
          component_ready('proxy', id);
        </script>
		
	</article>


	  <a class="mobile-only" name="jumpbottom"></a>
	  <a class="mobile-only" href="#jumptop" >Back to top</a>

	<footer>
	
		<div class="no-mobile"><p>&copy; Callum Dickinson and Francesca Moore 2015 <?php echo date("d/m/Y");?></p></div> <!---copyright and date will not show on mobile--->
	</footer>
</body>