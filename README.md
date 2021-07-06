# Active Campaign

Laravel integration for the [Active Campaign](https://developers.activecampaign.com/reference) package.

An open source package by **Digital Leap Agency**, code well, rock on!

## Documentation

Follow below steps to install and implement active campaign package in your laravel project:

1. Open Command-Prompt/terminal from your laravel project root folder.
2. Run following command to install the package:

> composer require digitalleapagency/active-campaign

3. Once the package is installed completely, you need to copy the configuration file inside your config folder and register the ActiveCampaignServerProvider. To do so, run the following command:

> php artisan activecampaign:install

4. Once the dependencies are installed and the facades, migrations are registered via service provider, you need to run the migrations by running the command:

> php artisan migrate

This will create all the necessary tables to store the active campaign contacts, tags and events. Make sure your laravel project is connected to a database.

5. Voila! You are now ready to use this package's Facades in your project.

Below mentioned are Facades and the method definition:

* use Contact;
	* Contact::addContact(array $contact[firstName,lastName,email,phone])
	* Contact::updateContact(array $contact[firstName,lastName,email,phone],$contactID)
	* Contact::getContacts(array $query_parameters) - visit [Active Campaign](https://developers.activecampaign.com/reference#list-all-contacts) for query parameters

* use Tag;
	* Tag::addTag(array $tag[tag,description],$contactID)
	* Tag::updateTag(array $tag[tag,description],$tagID,$contactID)
	* Tag::removeTag($tagID,$contactID)

* use Event;
	* Event::addEvent(array $event[event_name])
	* Event::trackEvent(array $event[event,eventdata,email])
