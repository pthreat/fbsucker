<?php

	namespace stange\fbsucker\entity\profile{

		use \stange\fbsucker\entity\profile\User			as UserEntity;
		use \stange\fbsucker\entity\profile\Page			as PageEntity;
		use \stange\fbsucker\entity\profile\Event			as	EventEntity;
		use \stange\fbsucker\entity\profile\Group			as	GroupEntity;
		use \stange\fbsucker\entity\profile\Application	as	ApplicationEntity;

		/**
		 * The request is needed in order to query the object id type
		 * We will ask the Graph API what type of object is it
		 */

		use \stange\fbsucker\http\Request;

		class Factory{

			private	$request		=	NULL;
			private	$objectId	=	NULL;
			private	$fields		=	Array();

			public function __construct(Request $request,$objectId){

				$this->request		=	$request;
				$this->objectId	=	$objectId;

			}

			private function getObjectType(){

				return	strtolower(
											$this->request
											->request($this->objectId)
											->getGraphData()
											->metadata
											->type
				);

			}

			public function build(Array $fields=Array()){

				$type	=	$this->getObjectType();

				switch($type){

					case 'user':

						$default	=	[
											'id','cover','birthday','about','link','likes','name','website','hometown','gender',
											'interested_in','is_verified','labels','languages','last_name','first_name','location',
											'meeting_for','political','public_key','religion','significant_other','sports',
											'work','accounts','achievements','groups','movies','music','picture','television',
											'books','favorite_teams'
						];

						$entity	=	new UserEntity();

					break;

					case 'group':
	
						$default	=	[
											'id','cover','description','email','icon','link','member_request_count',
											'name','owner','parent','privacy','purpose','updated_time','venue'
						];

						$entity	=	new GroupEntity();

					break;

					case 'page':

						$default	=	[
											'id','about','affiliation','app_id','app_links','artists_we_like',
											'attire','awards','band_interests','band_members','best_page',
											'bio','birthday','booking_agent','built','business','can_checkin',
											'category','category_list','checkins','company_overview','contact_address',
											'cover','culinary_team','current_location','description','directed_by',
											'display_subtext','displayed_message_response_time','emails',
											'fan_count','featured_video','features','food_styles','founded',
											'general_info','general_manager','genre','global_brand_page_name',
											'global_brand_root_id','has_added_app','hometown','hours',
											'impressum','is_always_open','is_community_page','is_permanently_closed',
											'is_published','is_unclaimed','is_verified','is_webhooks_subscribed',
											'link','location','members','influences','mission','mpg','name',
											'name_with_location_descriptor','network','overall_star_rating',
											'parking','parent_page','personal_info','personal_interests',
											'pharma_safety_info','phone','place_type','plot_outline',
											'press_contact','price_range','produced_by','products',
											'public_transit','publisher_space','rating_count',
											'record_label','release_date','restaurant_services',
											'restaurant_specialties','schedule','screenplay_by',
											'season','single_line_address','starring','start_info',
											'store_location_descriptor','store_number','studio',
											'talking_about_count','username','verification_status','voip_info','website',
											'were_here_count','written_by','likes'
						];

						$entity	=	new PageEntity();

					break;

					case 'application':

						$default	=	[
						];

						$entity	=	new AppEntity();

					break;

					case 'event':

						$default	=	[
						];

						$entity	=	new EventEntity();

					break;

					default:
						throw new \InvalidArgumentException("Unknown object type \"$type\"");
					break;

				}

				$fields	=	$fields	?	$fields	:	$default;

				$entity->setGraphData(
											$this->request
											->request($this->objectId,$fields)
											->getGraphData()
				);

				return $entity;

			}

		}

	}
