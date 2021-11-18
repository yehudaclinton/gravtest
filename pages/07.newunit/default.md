---
routable: true
title: 'Submit new listing'
template: form
visible: true
pageconfig:
    parent: /submitted-units/drafts
pagefrontmatter:
    visible: true
    status: draft
    template: default

form:
    name: newunit
    fields:
        -
            name: name
            label: Name
            type: text
            validate:
                required: true
        -
            name: title
            label: Title
            type: text
            validate:
                required: true
        -
            name: content
            label: 'Apartment details'
            type: textarea
            size: long
            classes: editor
            validate:
                required: true
        -
            name: attachments
            label: 'Attachment (images only)'
            type: file
            multiple: true
            accept:
                - 'image/*'
            validate:
                required: false
        -
            name: honeypot
            type: honeypot
    buttons:
        -
            type: submit
            value: Submit
    process:
        -
            addpage: null
        -
            redirect: '@self'
---

Please write unit details and attach any images and/or files.
