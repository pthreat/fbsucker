<?php

	namespace stange\fbsucker\console\command{

		use \stange\fbsucker\http\adapter\Basic			as BasicAdapter;
		use \stange\fbsucker\request\app\Token				as	AppToken;
		use \stange\fbsucker\http\Request					as	GraphRequest;
		use \stange\fbsucker\entity\Profile					as	ProfileEntity;
		use \stange\fbsucker\parser\photo\Metadata		as	MetadataParser;

		use \Aizuyan\Image2Ascii\Image2Ascii;

		use \Symfony\Component\Console\Command\Command;
		use \Symfony\Component\Console\Input\InputArgument;
		use \Symfony\Component\Console\Input\InputDefinition;
		use \Symfony\Component\Console\Input\InputInterface;
		use \Symfony\Component\Console\Input\InputOption;
		use \Symfony\Component\Console\Output\OutputInterface;
		use \Symfony\Component\Console\Formatter\OutputFormatterStyle;

		class Profile extends Command{

			private	$dir		=	NULL;
			private	$input	=	NULL;

			public function configure(){

				$r	=	InputOption::VALUE_REQUIRED;
				$o	=	InputOption::VALUE_OPTIONAL;

				$inputDefinition	=	Array(
													new InputOption('profile', 'p',$r,'Profile id or facebook username'),
													new InputOption('ascii', 'a',$o,'Print images as ASCII'),
													new InputOption('metadata', 'm',$r,'Parse phots metadata'),
													new InputOption('id', 'i',$r,'Application Id'),
													new InputOption('secret', 'k',$r,'Application secret'),
													new InputOption('fields','d',$o,'Fields to be fetched'),
													new InputOption('write-request','w',$o,'Save graph data to a JSON file')
				);

				$this->setName('profile:info')
				->setDescription('Fetch information regarding a facebook profile')
				->setDefinition(new InputDefinition($inputDefinition));

			}

			protected function execute(InputInterface $input, OutputInterface $output){

				$this->input	=	$input;
				$this->initializeStyles($output);

				$profile		=	$input->getOption('profile');
				$id			=	$input->getOption('id');
				$secret		=	$input->getOption('secret');
				$fields		=	$input->getOption('fields');


				if(!$fields){

					$fields	=	[	
										'id','cover','birthday','description','about','username',
										'category','link','were_here_count','likes','checkins','name',
										'parking','has_added_app','name_with_location_descriptor',
										'talking_about_count','is_published','is_community_page',
										'website'
					];

				}

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

				$this->createCacheDirectory($profile);

				$entity	=	new ProfileEntity(
														$this->request(
																			$request,
																			'profile.json',
																			$profile,
																			$fields
														)
				);

				$this->profileBasicsBlock($entity);

				$output->writeln("");

				$this->socialMediaBlock($entity);

				$output->writeln("");

				$this->likesBlock($entity);

				$output->writeln("");

				$this->photosBlock($entity);

			}

			private function isInCache($file){
	
				return file_exists("{$this->dir}/$file");

			}

			private function request(GraphRequest &$request, $file,$objectId=NULL,$fields=NULL){

				if($this->isInCache($file)){
					
					$request->getGraphData()->fromJSON("{$this->dir}/$file");
					return $request->getGraphData();

				}

				$request->request($objectId,$fields);
				$request->getGraphData()->save("{$this->dir}/$file");

				return $request->getGraphData();

			}

			public function createCacheDirectory($profile){

				$dir	=	realpath(__DIR__.'/../../..');
				$dir	=	"$dir/cache/$profile";

				if(!is_dir($dir)){

					mkdir($dir,0777,TRUE);

				}

				$this->dir	=	$dir;

			}

			private function printPic($pic){
	
				$this->output->writeln('8<------------------------------- ASCII ART');
				$img2Ascii = new Image2Ascii($pic);
				$this->output->writeln($img2Ascii->createImage()->createPixel()->scale(8, 8)->out());
				$this->output->writeln('------------------------------->8 ASCII ART');

			}

			public function printMetadata($file){

				$o	=	$this->output;
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

			private function saveCache($url){

				if(!($this->input->getOption('metadata') || $this->input->getOption('ascii'))){

					return FALSE;

				}

				$file	=	basename($url);
				$file	=	substr($file,0,strpos($file,'?'));

				if(!file_exists("{$this->dir}/$file")){

					$this->output->write("Saving $url to cache ...");

					file_put_contents("{$this->dir}/$file",file_get_contents($url));

				}

				if($this->input->getOption('ascii')){

					$this->printPic("{$this->dir}/$file");

				}

				if($this->input->getOption('metadata')){

					$this->printMetadata("{$this->dir}/$file");

				}

			}

			private function profileBasicsBlock($entity){

				$o	=	$this->output;


				$this->writeHeader($entity->getName());

				$o->writeln("");

				$o->writeln("Cover Pic\t\t:\t{$entity->getCover()}");

				$this->saveCache($entity->getCover());

				$o->writeln("Birthday\t:\t{$entity->getBirthday()->format('d/m/Y')}");
				$o->writeln("Zodiac\t\t:\t{$entity->getZodiacSign()}");
				$o->writeln("Category\t:\t{$entity->getCategory()}");
				$o->writeln("Checkins\t:\t{$entity->getCheckins()}");
				$o->writeln("Published\t:\t{$entity->isPublished()}");
				$o->writeln("Talking about\t:\t{$entity->getPeopleTalkingAboutCount()} people");
				$o->writeln("Profile link\t:\t{$entity->getLink()}");
				$o->writeln("");
				$this->writeLine();

			}

			private function socialMediaBlock($entity){

				$o	=	$this->output;
				$s	=	$entity->getSocialMedia();

				$this->writeHeader("Social media");
				$o->writeln("");
				$o->writeln("Website\t\t:\t{$entity->getWebsite()}");
				$o->writeln("Youtube\t\t:\t{$s->hasNetwork('youtube')}");
				$o->writeln("Twitter\t\t:\t{$s->hasNetwork('twitter')}");
				$o->writeln("Instagram\t:\t{$s->hasNetwork('instagram')}");
				$o->writeln("Keek\t\t:\t{$s->hasNetwork('keek')}");
				$o->writeln("Whatsapp\t:\t{$s->hasNetwork('whatsapp')}");
				$o->writeln("Email\t\t:\t{$s->getEmail()}");
				$o->writeln("");
				$this->writeLine();

			}

			private function likesBlock($entity){

				$o	=	$this->output;

				$this->writeHeader("People who like {$entity->getName()}");
				$o->writeln("");

				$likes	=	$entity->getLikes();

				//do{

					foreach($likes->getIterator() as $like){

						$o->writeln(sprintf("%s\t:\t%s",$like->id,$like->name));

					}

				//}while($likes->next());

				$o->writeln("");
				$this->writeLine();

			}

			public function photosBlock($entity){

				$o	=	$this->output;

				$this->writeHeader("{$entity->getName()} Photos");
				$o->writeln("");

				$photos	=	$entity->getPhotos();

				$data		=	$this->request(
													$photos->getRequest(),
													'photos.json'
				);

				//do{

				foreach($data->getIterator() as $photo){

					var_dump($photo);
					die();

				}

			}

			private function initializeStyles($output){

				$output->getFormatter()
				->setStyle(
								'header', 
								new OutputFormatterStyle('white', 'blue', array('bold'))
				);

				$output->getFormatter()
				->setStyle(
								'line', 
								new OutputFormatterStyle('white', 'white')
				);

				$output->getFormatter()
				->setStyle(
								'rheader', 
								new OutputFormatterStyle('white', 'red', array('bold'))
				);

				$this->output	=	$output;

			}

			public function writeHeader($title,$width=80){

				$this->output->writeln(sprintf("<header>%s</header>",str_pad($title,$width)));

			}

			public function writeRedHeader($title,$width=80){

				$this->output->writeln(sprintf("<rheader>%s</rheader>",str_pad($title,$width)));

			}

			public function writeLine($width=80){

				$this->output->writeln(sprintf("<line>%s</line>",str_pad(' ',$width)));

			}

		}

	}

