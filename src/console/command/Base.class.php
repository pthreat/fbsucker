<?php

	namespace stange\fbsucker\console\command{

		use \Aizuyan\Image2Ascii\Image2Ascii;
		use \stange\fbsucker\parser\photo\Metadata		as	MetadataParser;
		use \stange\fbsucker\http\Request					as	GraphRequest;
		use \stange\fbsucker\Entity							as	AbstractEntity;
		use \stange\fbsucker\http\adapter\Basic			as BasicAdapter;
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
				$this->initializeStyles();
				$this->output->writeln($this->printBanner());

				$profile		=	$input->getOption('profile');

				$id			=	$this->input->getOption('id');
				$secret		=	$this->input->getOption('secret');
				$fields		=	$this->input->getOption('fields');

				/**
				 * Create the application access token
				 */

				$token	=	new AppToken([
													'id'		=>	$id,
													'secret'	=>	$secret,
				]);

				/**
				 * Create the graph request
				 */

				$request		=	new GraphRequest([
															"adapter"	=>	new BasicAdapter(),
															"token"		=>	$token,
															"cache"		=>	new RequestCache([
																										"dir"		=>	"cache",
															])
				]);

				$factory			=	new ProfileFactory($request,$profile);

				$this->__execute(
										$factory->build(
																$fields==NULL ? [] : $fields
										)
				);

			}

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

			abstract protected function __execute(AbstractEntity $entity);

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
