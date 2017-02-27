<?php

	namespace stange\fbsucker\entity{

		use \stange\fbsucker\Entity					as	AbstractEntity;
		use \stange\fbsucker\entity\Comment			as	CommentEntity;
		use \stange\fbsucker\entity\Place			as	PlaceEntity;
		use \stange\fbsucker\collection\Profile	as	ProfileCollection;
		use \stange\fbsucker\collection\Comment	as	CommentCollection;

		class Event extends AbstractEntity{

			public function getPlace(){

				$place	=	new PlaceEntity();
				$place->setGraphData($this->getGraphAttribute('place'));
				return $place;

			}

			public function getAttendingCount(){
		
				return $this->getGraphAttribute('attending_count');
				
			}

			public function getInterestedCount(){
		
				return $this->getGraphAttribute('interested_count');
				
			}

			public function getMaybeCount(){
		
				return $this->getGraphAttribute('maybe_count');
				
			}

			public function getNoReplyCount(){
		
				return $this->getGraphAttribute('noreply_count');
				
			}

			public function isCanceled(){

				return $this->getGraphAttribute('is_canceled');

			}

			public function getDeclinedCount(){
		
				return $this->getGraphAttribute('declined_count');
				
			}

			public function getAttending(){

				$pc	=	new ProfileCollection();
	
				foreach($this->getGraphAttribute('attending') as $profile){

					$pc->add(new Profile($profile));

				}

				return $pc;

			}

			public function getAdmins(){

				$collection	=	new ProfileCollection();

				foreach($this->getGraphAttribute('admins') as $data){

					$admin	=	new ProfileEntity();
					$admin->setGraphData($data);
					$collection->add($admin);

				}

				return $collection;

			}

			public function getComments(){

				$cc	=	new CommentCollection();

				foreach($this->getGraphAttribute('feed')->data as $c){

					$comment	=	new CommentEntity();
					$comment->setGraphData($c);

					$cc->add($comment);

				}

				return $cc;

			}

			public function getAttendingCount(){

				return $this->getGraphAttribute('attending_count');

			}

			public function getNoReply(){

				$pc	=	new ProfileCollection();
	
				foreach($this->getGraphAttribute('noreply') as $profile){

					$pc->add(new Profile($profile));

				}

				return $pc;

			}

			public function getNoReplyCount(){

				return $this->getGraphAttribute('noreply_count');

			}

			public function getDeclined(){

				$pc	=	new ProfileCollection();
	
				foreach($this->getGraphAttribute('declined') as $profile){

					$pc->add(new Profile($profile));

				}

				return $pc;

			}

			public function getDeclinedCount(){

				return $this->getGraphAttribute('declined_count');

			}

			public function getMaybe(){

				$pc	=	new ProfileCollection();
	
				foreach($this->getGraphAttribute('maybe') as $profile){

					$pc->add(new Profile($profile));

				}

				return $pc;

			}

			public function getStartTime($format=NULL){

				$start	=	\DateTime::createFromFormat(\DateTime::ATOM,$this->getGraphAttribute('start_time'));

				if(!$start){

					throw new \InvalidArgumentException("Failed to create event start time object");

				}

				return $format ? $format->format($format) : $start;

			}

			public function getEndTime($format=NULL){

				$end	=	\DateTime::createFromFormat(\DateTime::ATOM,$this->getGraphAttribute('end_time'));

				if(!$end){

					throw new \InvalidArgumentException("Failed to create event end time object");

				}

				return $format ? $format->format($format) : $end;

			}

			public function getDuration($format='%h'){

				$start	=	$this->getStartTime();
				$diff		=	$start->diff($this->getEndTime());

				return $diff->format($format);

			}

			public function __toString(){

				try{

					return $this->getName();

				}catch(\Exception $e){

					return '';

				}

			}

		}

	}
