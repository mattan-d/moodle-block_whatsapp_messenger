# WhatsApp Messenger Block for Moodle

A Moodle block plugin that allows teachers to send WhatsApp messages to students enrolled in their courses.

## Features

- Send WhatsApp messages to individual students or all students in a course
- Uses WhatsApp Business API (Facebook Graph API)
- Plugin-level settings for WhatsApp credentials
- Support for WhatsApp message templates
- Message logging and tracking
- Only sends to students with valid phone numbers

## Requirements

- Moodle 3.10 or later
- WhatsApp Business Account
- WhatsApp Business API access token
- WhatsApp Business Phone Number ID
- Students must have phone numbers in their profiles

## Installation

1. Download or clone this plugin to `blocks/whatsapp_messenger`
2. Log in as administrator
3. Visit Site Administration → Notifications to complete installation
4. Configure WhatsApp credentials in Site Administration → Plugins → Blocks → WhatsApp Messenger

## Configuration

Navigate to **Site Administration → Plugins → Blocks → WhatsApp Messenger** and configure:

1. **WhatsApp Access Token**: Your WhatsApp Business API access token from Facebook
2. **WhatsApp Phone Number ID**: Your WhatsApp Business phone number ID
3. **Template Name**: (Optional) WhatsApp message template name
4. **Template Language**: (Optional) Template language code (e.g., en_US)
5. **Use Template**: Enable to use templates instead of free text

## Usage

1. Add the "WhatsApp Messenger" block to a course page
2. Select students from the dropdown (or select "All Students")
3. Type your message
4. Click "Send WhatsApp Message"
5. View delivery status in the block

## Phone Number Format

- Phone numbers must include country code
- Remove all spaces and special characters
- Example: 972501234567 (for Israel)

## Capabilities

- `block/whatsapp_messenger:addinstance` - Add block to course
- `block/whatsapp_messenger:sendmessage` - Send WhatsApp messages

## License

GPL v3 or later

## Author

Created for Moodle course communication
