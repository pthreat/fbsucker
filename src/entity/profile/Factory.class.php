<?php

	/**
	 * When a request is made to the Facebook Graph API, we pass an object id, for instance
	 * "1234567". The problem here is that 1234567 could be anything, a User, an Event, a Group
	 * a Page, etc. 
	 *
	 * This factory solves that problem by fetching the metadata->type parameter from the request
	 * and checking what kind of object are we querying *BEFORE* deciding *WHAT* entity type to build.
	 *
	 * After analyzing the object type, we can set in the *PROPER* ->fields<- to be queried for said object
	 * id. 
	 * 
	 * -> In this fashion we can get the most of each facebook object. <-
	 *
	 * The only problem with this approach is that we need to make a Graph Request to the object first, and then
	 * perform another request with the proper fields for the detected object type.
	 * But as we do not know the object id entered by user input before hand, we have to autodetect the object type
	 * first and perform this extra (small) request in order to detect the object type.
	 *
	 * As the namespace states, this factory handles profile objects only.
	 *
	 */

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

			/**
			 * Provide some default fields for each different profile type
			 * These fields are not definitive since you can add your own fields passing them to 
			 * the build method of this class.
			 *
			 * @see self::build
			 */

			private	$fields		=	[
												"user"	=>	[
																'id','cover','birthday','about','link','likes','name',
																'website','hometown','gender',
																'interested_in','is_verified','labels','languages',
																'last_name','first_name','location',
																'meeting_for','political','public_key','religion',
																'significant_other','sports','work','accounts',
																'achievements','groups','movies','music','picture','television',
																'books','favorite_teams'
												],
												'group'	=>	[
																'id','cover','description','email','icon','link','member_request_count',
																'name','owner','parent','privacy','purpose','updated_time','venue'
												],
												'page'	=>	[
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
																'talking_about_count','username','verification_status',
																'voip_info','website','were_here_count','written_by','likes'
											],
											'app'			=>	[],
											'event'		=>	[]
			];

			public function __construct(Request $request,$objectId,Array $fields=Array()){

				$this->request		=	$request;
				$this->objectId	=	$objectId;

				if($fields){
	
					$this->setFields($fields);

				}

			}

			public function setFields(Array $fields){

				foreach($fields as $profileType=>$fields){

					if(isset($this->fields[$entityName])){

						$this->fields[$profileType]	=	explode(',',$fields);

					}

				}

				return $this;

			}

			public function getFields(){

				return $this->fields;

			}

			/**
			 * For defining which fields should be queried everytime for each entity type,
			 * it is convenient to have an ini configuration file laying somewhere in order
			 * not to be running the fbsucker command and specifying which fields should be queried
			 * for each entity type through the command line. 
			 *
			 * In short words we want to avoid this:
			 *--------------------------------------------------------
			 *
			 * php bin/fbsucker --profile 123456 --fields a,b,c,d,e,f,c,(4000 fields more)
			 *
			 * And make it more like this ...
			 *--------------------------------------------------------
			 * 
			 * php bin/fbsucker --profile 123456 --fields-from-file config/fields.ini
			 *
			 * Example ini file structure
			 *--------------------------------------------------------
			 *
			 * [profile_fields]
			 *    user  = field1,field2,field3
			 *    group = field1,field2,field3
			 *    page  = field1,field2,field3
			 *    app   = field1,field2,field3
			 *    event = field1,field2,field3
			 */

			public static function fromIniFile($file,Request $request){

				if(!file_exists($file)){

					throw new \InvalidArgumentException("File \"$file\" does not exists");

				}

				$config	=	parse_ini_file($file,$sections=TRUE);

				if(!$config){

					throw new \RuntimeException("Could not parse ini file \"$file\"");	

				}

				if(!isset($file['profile_fields'])){

					$msg	=	"Could not find section named profile_fields in ini file \"$file\"";
					throw new \LogicException($msg);

				}

				foreach($file['profile_fields'] as &$field){

					$field	=	explode(',',$field);

				}

				return new static($request,$objectId,$file['profile_fields']);

			}

			/** 
			 * Perform a simple Graph Request specifying the object id to be queried
			 * and return the metadata "type" for us being able to tell which kind 
			 * of object we are talking about (user, group, app, etc).
			 *
			 * @return string the Graph object type
			 */

			private function getObjectType(){

				return	strtolower(
											$this->request
											->request($this->objectId)
											->getGraphData()
											->metadata
											->type
				);

			}

			/**
			 * Build the profile entity
			 *
			 * @param Array $fields Manually pass in which fields should be queried
			 */

			public function build(Array $fields=Array()){

				/** Get which object type is the passed in profile id **/

				$type				=	$this->getObjectType();

				/** Create the full fbsucker namespace to the entity type **/

				$entityClass	=	sprintf("\\stange\\fbsucker\\entity\\profile\\%s",ucwords($type));

				/** If the object type doesn't translates to a fbsucker profile entity throw an exception**/

				if(!class_exists($entityClass)){

					throw new \InvalidArgumentException("Unknown object type \"$type\"");

				}

				/** Create an instance of the profile entity object  **/
				$entity	=	new $entityClass();

				/** Determine if custom fields where passed as the build parameter **/

				$fields	=	$fields	?	$fields	:	$this->fields[$type];

				/** Perform the request and set the graphData into the fbsucker entity **/

				$entity->setGraphData(
											$this->request
											->request($this->objectId,$fields)
											->getGraphData()
				);

				/** Return the entity with the graph data set into it **/

				return $entity;

			}

		}

	}
