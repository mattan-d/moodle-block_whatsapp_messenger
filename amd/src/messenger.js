// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript module for WhatsApp Messenger block.
 *
 * @package    block_whatsapp_messenger
 * @copyright  2024 CentricApp LTD (https://centricapp.co.il)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    return {
        init: function(courseid) {
            $('#whatsapp-message-form').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var recipient = $('#recipient-select').val();
                var message = $('#message-text').val();

                if (!message.trim()) {
                    Notification.addNotification({
                        message: 'Please enter a message',
                        type: 'error'
                    });
                    return;
                }

                var $btn = $('#send-whatsapp-btn');
                var $status = $('#whatsapp-status');

                $btn.prop('disabled', true).text('Sending...');
                $status.removeClass('alert-success alert-danger').text('');

                // Use Moodle's AJAX API with External Services
                Ajax.call([{
                    methodname: 'block_whatsapp_messenger_send_message',
                    args: {
                        courseid: courseid,
                        recipient: parseInt(recipient),
                        message: message
                    },
                    done: function(response) {
                        if (response.success) {
                            $status.addClass('alert alert-success').text(response.message);
                            $('#message-text').val('');
                            
                            Notification.addNotification({
                                message: response.message,
                                type: 'success'
                            });
                        } else {
                            $status.addClass('alert alert-danger').text(response.message);
                            Notification.addNotification({
                                message: response.message,
                                type: 'error'
                            });
                        }
                    },
                    fail: function(error) {
                        var errorMsg = 'Error sending message';
                        if (error.message) {
                            errorMsg = error.message;
                        }
                        $status.addClass('alert alert-danger').text(errorMsg);
                        Notification.addNotification({
                            message: errorMsg,
                            type: 'error'
                        });
                    },
                    always: function() {
                        $btn.prop('disabled', false).text('Send Message');
                    }
                }]);

                return false;
            });
        }
    };
});
