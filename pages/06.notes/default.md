---
title: notes
process:
    markdown: true
    twig: true
---

twig stuff {{ example() }} after

## How to add custom field to user registration
add a field to login plugin settings
add the field to user/plugins/login/pages/register.md
add field to system/blueprints/user/account.yaml
TODO proper languages

## fix the double hero image in catalog
comment
user/themes/quark/templates/partials/blog-list-item.html.twig

## How to add stuff like pictures on newunit update
after add it after

honeypot: ''
\---

## How to keep plugin customizations through an update
Changes made in the plugin options in the Admin panel will be saved to the configuration file in that location.

If there are no options try this
Copy the add-page-by-form.yaml default file to your user/config/plugins folder and use that copy to change configuration settings.

## Theme customization child-theme (inherited)
Copy all the options from the theme YAML file you are inheriting from (or from the user/config/themes folder if you have customized it) at the top of the newly created YAML configuration file of your theme: /user/themes/mytheme/mytheme.yaml.
Copy the “form” section from /user/themes/quark/blueprints.yaml file into /user/themes/mytheme/blueprints.yaml in order to include the customizable elements of the theme in the admin. (Or simply replace the file and edit its content.)
Change your default theme to use your new mytheme by editing the pages: theme: option in your user/config/system.yaml configuration file:

## Huge dev reference
https://learn.getgrav.org/17/api#class-grav-common-data-data