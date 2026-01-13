# WhatsApp Messenger Block for Moodle

A Moodle block plugin that enables teachers to send WhatsApp messages to students enrolled in their courses using the WhatsApp Business API.

## Features

- Send WhatsApp messages to individual students or all enrolled students
- Plugin-level configuration for WhatsApp Business API credentials
- Message logging for tracking sent messages
- AJAX-based interface with real-time status updates
- Uses students' phone numbers from their Moodle profile

## Requirements

- Moodle 3.9 or higher
- WhatsApp Business API account
- Access token and Phone Number ID from WhatsApp Business

## Installation

1. Copy the plugin folder to your Moodle installation: `{moodle}/blocks/whatsapp_messenger/`
2. Log in as administrator and visit the notifications page to install
3. Configure the plugin settings with your WhatsApp Business API credentials

## Configuration

Go to **Site administration > Plugins > Blocks > WhatsApp Messenger** and configure:

- **Access Token**: Your WhatsApp Business API access token
- **Phone Number ID**: Your WhatsApp Business phone number ID
- **API Version**: WhatsApp API version (default: v17.0)

## Usage

1. Add the block to a course page
2. Select a recipient (individual student or all students)
3. Type your message
4. Click "Send Message"

The plugin will send messages to students who have phone numbers in their profile (phone1 or phone2 field).

## Permissions

- `block/whatsapp_messenger:addinstance` - Add the block to a course
- `block/whatsapp_messenger:sendmessage` - Send WhatsApp messages

## Database

The plugin creates a log table `block_whatsapp_messenger_log` to track all sent messages.

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License.
