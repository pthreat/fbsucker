<?php

	namespace stange\fbsucker\console\command{

		use \Aizuyan\Image2Ascii\Image2Ascii;
		use \stange\fbsucker\parser\photo\Metadata		as	MetadataParser;
		use \stange\fbsucker\http\Request					as	GraphRequest;
		use \stange\fbsucker\Entity							as	AbstractEntity;
		use \stange\fbsucker\http\adapter\Basic			as BasicAdapter;
		use \stange\fbsucker\request\app\Token				as	AppToken;
		use \stange\fbsucker\entity\profile\Factory		as	ProfileFactory;

		use \Symfony\Component\Console\Command\Command;
		use \Symfony\Component\Console\Input\InputArgument;
		use \Symfony\Component\Console\Input\InputDefinition;
		use \Symfony\Component\Console\Input\InputInterface;
		use \Symfony\Component\Console\Input\InputOption;
		use \Symfony\Component\Console\Output\OutputInterface;
		use \Symfony\Component\Console\Formatter\OutputFormatterStyle;

		abstract class Base extends Command{

			private		$cacheDir	=	NULL;
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

				$this->createCacheDirectory($profile);

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

			protected function isInCache($file){
	
				return file_exists("{$this->cacheDir}/$file");

			}

			protected function request(GraphRequest &$request, $file,$objectId=NULL,$fields=NULL){

				if($this->isInCache($file)){
					
					$request->getGraphData()->fromJSON("{$this->cacheDir}/$file");
					return $request->getGraphData();

				}

				$request->request($objectId,$fields);
				$request->getGraphData()->save("{$this->cacheDir}/$file");

				return $request->getGraphData();

			}

			public function createCacheDirectory($profile){

				$dir	=	realpath(__DIR__.'/../../..');
				$dir	=	"$dir/cache/$profile";

				if(!is_dir($dir)){

					mkdir($dir,0777,TRUE);

				}

				$this->cacheDir	=	$dir;

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

			protected function saveCache($url){

				if(!($this->input->getOption('metadata') || $this->input->getOption('ascii'))){

					return FALSE;

				}

				$file	=	basename($url);
				$file	=	substr($file,0,strpos($file,'?'));

				if(!file_exists("{$this->cacheDir}/$file")){

					$this->output->write("Saving $url to cache ...");

					file_put_contents("{$this->cacheDir}/$file",file_get_contents($url));

				}

				if($this->input->getOption('ascii')){

					$this->printPic("{$this->cacheDir}/$file");

				}

				if($this->input->getOption('metadata')){

					$this->printMetadata("{$this->cacheDir}/$file");

				}

			}

		}

	}
