<?php

	namespace stange\fbsucker\console\command{

		use \Aizuyan\Image2Ascii\Image2Ascii;
		use \stange\fbsucker\parser\photo\Metadata		as	MetadataParser;
		use \stange\fbsucker\http\Request					as	GraphRequest;
		use \stange\fbsucker\Entity							as	AbstractEntity;
		use \stange\fbsucker\http\adapter\Basic			as BasicHttpAdapter;
		use \stange\fbsucker\request\app\Token				as	AppToken;
		use \stange\fbsucker\entity\profile\Factory		as	ProfileFactory;
		use \stange\fbsucker\http\request\Cache			as	RequestCache;

		use \Symfony\Component\Console\Command\Command;
		use \Symfony\Component\Console\Input\InputArgument;
		use \Symfony\Component\Console\Input\InputDefinition;
		use \Symfony\Component\Console\Input\InputInterface;
		use \Symfony\Component\Console\Input\InputOption;
		use \Symfony\Component\Console\Output\OutputInterface;
		use \Symfony\Component\Console\Formatter\OutputFormatterStyle;

		abstract class Base extends Command{

			protected	$input		=	NULL;
			protected	$output		=	NULL;
			protected	$entity		=	NULL;

			/**
			 * Initialize command line styles
			 */

			private function initializeStyles(){

				$this->output->getFormatter()
				->setStyle(
								'header', 
								new OutputFormatterStyle('white', 'blue', array('bold'))
				);

				$this->output->getFormatter()
				->setStyle(
								'line', 
								new OutputFormatterStyle('white', 'white')
				);

				$this->output->getFormatter()
				->setStyle(
								'rheader', 
								new OutputFormatterStyle('white', 'red', array('bold'))
				);

				$this->output->getFormatter()
				->setStyle(
								'pheader', 
								new OutputFormatterStyle('white', 'magenta', array('bold'))
				);

				$this->output->getFormatter()
				->setStyle(
								'yheader', 
								new OutputFormatterStyle('black', 'yellow')
				);

			}

			/**
			 * Print a random ASCII art banner at startup by taking the banner.ascii files located
			 * at the src/console/banner folder.
			 */

			private function printBanner(){
		
				$bannersPath	=	__DIR__.'/../banners';
				$dp				=	opendir($bannersPath);

				$banners	=	Array();

				while($file = readdir($dp)){

					$file = "$bannersPath/$file";

					if(is_dir($file)){

						continue;

					}

					$banners[]	=	$file;

				}

				shuffle($banners);

				return file_get_contents($banners[0]);

			}

			protected function writeHeader($title,$width=80){

				$this->output->writeln(sprintf("<header>%s</header>",str_pad($title,$width)));

			}

			protected function writePurpleHeader($title,$width=80){

				$this->output->writeln(sprintf("<pheader>%s</pheader>",str_pad($title,$width)));

			}

			protected function writeRedHeader($title,$width=80){

				$this->output->writeln(sprintf("<rheader>%s</rheader>",str_pad($title,$width)));

			}

			protected function writeYellowHeader($title,$width=80){

				$this->output->writeln(sprintf("<yheader>%s</yheader>",str_pad($title,$width)));

			}

			protected function writeLine($width=80){

				$this->output->writeln(sprintf("<line>%s</line>",str_pad(' ',$width)));

			}

			protected function execute(InputInterface $input, OutputInterface $output){

				$this->input	=	$input;
				$this->output	=	$output;

				/** Initialize the application styles **/
				$this->initializeStyles();

				$this->output->writeln($this->printBanner());

				$profile	=	$input->getOption('profile');
				$fields	=	$this->input->getOption('fields');

				/**
				 * Create the application access token
				 */

				$token	=	new AppToken([
													'id'		=>	$this->input->getOption('id'),
													'secret'	=>	$this->input->getOption('secret')
				]);

				/**
				 * Create the basic http adapter
				 */

				 $adapter	=	new BasicHttpAdapter([
																"cache"	=>	new RequestCache([
																										"entryPoint" => "cache",
																])
				 ]);

				/**
				 * Create the graph request, set the previously created HTTP Adapter 
				 * and the app token into the Graph Request object.
				 */

				$request		=	new GraphRequest([
															"adapter"	=>	$httpAdapter,
															"token"		=>	$token
				]);

				/**
				 * All requests are profile related, since our main purpose, is to fetch profile related data.
				 *
				 * Through the profile factory the proper graph API fbsucker entity is created.
				 *
				 * For more information read the problem described at the profile factory class. 
				 *
				 * @see \stange\fbsucker\entity\profile\Factory
				 */

				$factory			=	new ProfileFactory($request,$profile);

				/**
				 * Call the __execute method defined in the derived command class
				 * As an argument, pass in the built request through the profile factory.
				 */

				$this->__execute(
										$factory->build(
																$fields==NULL ? [] : $fields
										)
				);

			}

			/**
			 * The abstract method __execute is used for each command line command to
			 * gather information and print it at their own will.
			 */

			abstract protected function __execute(AbstractEntity $entity);


			/**
			 * The following methods have to be moved to a helper class
			 */

			protected function printComments($comments){

				$o	=	$this->output;

				$this->writePurpleHeader("Comments section");

				foreach($comments->getIterator() as $c){

					$o->writeln("");
					$this->writeHeader("From\t:\t{$c->getFrom()}");
					$this->writeHeader("FBID\t:\t{$c->getFrom()->getId()}");
					$o->writeln("");
					$this->writeYellowHeader("Message");
					$o->writeln("");
					$o->writeln($c->getMessage());
					$o->writeln("");

				}

			}

			protected function printPic($pic){
	
				$this->output->writeln('8<------------------------------- ASCII ART');
				$img2Ascii = new Image2Ascii($pic);
				$this->output->writeln($img2Ascii->createImage()->createPixel()->scale(8, 8)->out());
				$this->output->writeln('------------------------------->8 ASCII ART');

			}

			protected function printMetadata($file){

				$o				=	$this->output;
				$metadata	=	new MetadataParser($file);

				$this->writeHeader('Picture metadata');	

				$o->writeln("GPS Latitude: {$metadata->getLatitude()}");
				$o->writeln("GPS Longitude: {$metadata->getLongitude()}");
				$o->writeln("Created Time: {$metadata->getCreatedAt('d/m/Y H:i:s')}");
				$o->writeln("Updated Time: {$metadata->getUpdatedAt('d/m/Y H:i:s')}");

				$this->writeRedHeader("Raw picture metadata");

				foreach($metadata->getData() as $key=>$value){

					$o->writeln("$key\t\t:\t$value");

				}

				$this->writeRedHeader("");
				$o->writeln("");

			}

		}

	}
