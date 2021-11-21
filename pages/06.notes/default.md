---
title: notes
---

## How to add custom field to user registration
add a field to login plugin settings
add the field to user/plugins/login/pages/register.md
add field to system/blueprints/user/account.yaml
TODO proper languages

## How to add stuff like pictures on newunit update
after add it after
honeypot: ''
---

## How to keep plugin customizations through an update
Changes made in the plugin options in the Admin panel will be saved to the configuration file in that location.

If there are no options try this
Copy the add-page-by-form.yaml default file to your user/config/plugins folder and use that copy to change configuration settings.

## Huge dev refference
https://learn.getgrav.org/17/api#class-grav-common-data-data