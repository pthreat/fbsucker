<?php

	namespace stange\fbsucker\console\command{

		use \stange\fbsucker\console\command\Base			as	BaseCommand;
		use \stange\fbsucker\Entity							as	AbstractEntity;

		use \Symfony\Component\Console\Command\Command;
		use \Symfony\Component\Console\Input\InputArgument;
		use \Symfony\Component\Console\Input\InputDefinition;
		use \Symfony\Component\Console\Input\InputInterface;
		use \Symfony\Component\Console\Input\InputOption;
		use \Symfony\Component\Console\Output\OutputInterface;
		use \Symfony\Component\Console\Formatter\OutputFormatterStyle;

		class Profile extends BaseCommand{

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
				);

				$this->setName('profile:info')
				->setDescription('Fetch information regarding a facebook profile')
				->setDefinition(new InputDefinition($inputDefinition));

			}

			protected function __execute(AbstractEntity $entity){

				$this->profileBasicsBlock($entity);

				$this->output->writeln("");
				$this->socialMediaBlock($entity);

				$this->output->writeln("");

				$this->likesBlock($entity);

				$this->output->writeln("");
				$this->writeLine();

			}

			private function profileBasicsBlock($entity){

				$o	=	$this->output;

				$this->writeHeader($entity->getName());

				$o->writeln("");

				$o->writeln("Cover Pic\t:\t{$entity->getCover()}");

				$this->saveCache($entity->getCover());

				$o->writeln("Birthday\t:\t{$entity->getBirthday()->format('d/m/Y')}");
				$o->writeln("Zodiac\t\t:\t{$entity->getZodiacSign()}");
				$o->writeln("Category\t:\t{$entity->getCategory()}");
				$o->writeln("Checkins\t:\t{$entity->getCheckins()}");
				$o->writeln("Published\t:\t{$entity->isPublished()}");
				$o->writeln("Talking about\t:\t{$entity->getPeopleTalkingAboutCount()} people");
				$o->writeln("Profile link\t:\t{$entity->getLink()}");
				$o->writeln("");

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

			}

			public function photosBlock($entity){

				$o	=	$this->output;

				$fields	=	[
									'id','link','comments','images','name','name_tags','place',
									'sharedposts','tags','updated_time','created_time'
				];

				$this->writeHeader("{$entity->getName()} Photos");
				$o->writeln("");

				$photos	=	$entity->getPhotos();

				$data		=	$this->request(
													$photos->getRequest(),
													'photos.json',
													NULL,
													$fields
				);

				//do{

				$this->writeHeader("{$entity->getName()} Photo stream");
				$o-writeln("");

				foreach($data->getIterator() as $photo){

					$this->saveCache($photo->getMaxQuality());
					$o->writeln("Max Quality: {$photo->getMaxQuality()}");
					$o->writeln("Available Qualities:{$photo->getQualitiesAsString()}");
					$this->printComments($photo->getComments());

				}

			}

		}

	}

