<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<link rel="stylesheet" href="style.css" type="text/css" />
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="index,follow" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php wp_title( '-', true, 'right' ); ?></title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat|Roboto:700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/allpages.css" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" />
</head>

<body>

	<section class="navigation clearfix">
	
		<div class="sitewidth">
	
			<a href="/" class="logo"><img src="<?php bloginfo('template_url'); ?>/img/martine-hoopt-op-beter.svg" alt="Martine hoopt op beter"></a>
		
			<nav>
				<ul>
					<li><a href="/" class="active">Home</a></li>
					<li><a href="/">Over mij</a></li>
					<li><a href="/">Help mij</a></li>
					<li><a href="/">Multiple sclerose</a></li>
					<li><a href="/">Contact</a></li>
				</ul>
			</nav>

		</div>
	
	</section>
	
	<section class="jumbophoto">
	
		<div class="sitewidth" style="background-image: url(<?php bloginfo('template_url'); ?>/img/martine-met-kinderen-op-strand.jpg)">
			<div class="dimmed">
		
				<h1>Help mij in mijn strijd tegen MS en de hoop op een beter leven!</h1>
				<p>Ondanks alle medicatie heeft MS na ruim zes jaar al heel veel van mijn mobiliteit afgenomen. Artsen in Nederland lijken mij niet te kunnen helpen en daarom heb ik mijn hoop volledig gevestigd op een stamceltherapie in Mexico. Help je mij mee op de kans op een beter leven?</p>
				
				<div class="buttons">
					<a href="#" class="btn">Lees verder</a>
				</div>

			</div>
		</div>

	</section>
	
	<section class="aboutme">
	
		<div class="sitewidth">
	
			<div class="text">
				<h2>Ik ben Martine</h2>
				<p>Als 41-jarige vrouw met twee schatten van dochters hoor je toch volop van het leven te genieten? Toch? Maar daar kwam zes jaar abrupt een streep doorheen toen bij mijn toenmalige vriend keelkanker werd geconstateerd en een maand later bij mij de diagnose MS werd gesteld.</p>
				
				<div class="buttons">
					<a href="#" class="btn">Lees verder</a>
				</div>
				
				<img src="<?php bloginfo('template_url'); ?>/img/portret-martine.jpg" class="portrait" alt="Portret Martine" />
			</div>

		</div>
	
	</section>

	<section class="crowdfunding clearfix">
	
		<div class="sitewidth">
	
			<div class="photo"><img src="<?php bloginfo('template_url'); ?>/img/clinica-ruiz.jpg" alt="Clínica Ruiz" /></div>
			
			<div class="text">

				<h2>Stamceltherapie</h2>
				<p>De stamceltherapie, welke in totaal vier weken duurt, in de <a href="http://www.clinicaruiz.com/inicio.php" target="_blank">Clínica Ruiz</a> kliniek in Mexico kost $54.500. Met de kosten van de nabehandeling, reiskosten en de benodigde MRI scan kan dit oplopen tot wel &euro; 65.000,-. Omdat ik dit niet alleen kan betalen ben ik een crowdfunding actie begonnen.</p>
				
				<div class="buttons">
					<a href="#" class="btn">Lees verder</a>
				</div>

			</div>
	
		</div>

	</section>

	<section class="aboutms">
	
		<div class="sitewidth">

			<div class="index accent clearfix">
			
				<ul>
					<li><a href="#">Wat is MS</a></li>
					<li><a href="#">Klachten bij MS</a></li>
					<li><a href="#">Vormen van MS</a></li>
					<li><a href="#">Hoe beïnvloed MS mijn leven</a></li>
				</ul>
				
			</div>
			
			<div class="text">
			
				<h2>Multiple sclerose</h2>
				<p>Multiple sclerose (MS) is een chronische aandoening van het centrale zenuwstelsel. Anders dan vaak wordt gedacht is multiple sclerose dus geen spierziekte. Bij MS gaat het isolerende laagje rondom de zenuwbanen (de myelineschede) langzaam stuk. Daardoor kunnen de zenuwprikkels niet meer goed geleid worden door de zenuwbanen.</p>
				
				<div class="buttons">
					<a href="#" class="btn">Lees verder</a>
				</div>

			</div>

		
		</div>
	
	</section>
	
	<footer>
		<p>&copy <?php echo Date("Y"); ?> - Website gratis aangeboden door <a href="http://www.virtualpages.nl" target="_blank"><img src="<?php bloginfo('template_url'); ?>/img/virtualpages.svg" class="logo" alt="Virtual Pages" /></a></p>
	</footer>

</body>

</html>