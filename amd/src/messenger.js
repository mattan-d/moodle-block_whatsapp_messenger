// This file is part of Moodle - http://moodle.org/

var define = window.define // Declare the define variable
var M = window.M // Declare the M variable

define(["jquery", "core/ajax", "core/notification"], ($, Ajax, Notification) => ({
  init: (courseid) => {
    $("#whatsapp-message-form").on("submit", (e) => {
      e.preventDefault()

      var recipient = $("#recipient-select").val()
      var message = $("#message-text").val()

      if (!message.trim()) {
        Notification.addNotification({
          message: "Please enter a message",
          type: "error",
        })
        return
      }

      var $btn = $("#send-whatsapp-btn")
      var $status = $("#whatsapp-status")

      $btn.prop("disabled", true).text("Sending...")
      $status.removeClass("alert-success alert-danger").text("")

      $.ajax({
        url: M.cfg.wwwroot + "/blocks/whatsapp_messenger/send_message.php",
        method: "POST",
        data: {
          courseid: courseid,
          recipient: recipient,
          message: message,
          sesskey: M.cfg.sesskey,
        },
        dataType: "json",
        success: (response) => {
          if (response.success) {
            var msg =
              "Sent: " + response.sent + ", Failed: " + response.failed + " out of " + response.total + " recipients"
            $status.addClass("alert alert-success").text(msg)
            $("#message-text").val("")

            Notification.addNotification({
              message: msg,
              type: "success",
            })
          } else {
            $status.addClass("alert alert-danger").text(response.error)
            Notification.addNotification({
              message: response.error,
              type: "error",
            })
          }
        },
        error: (xhr, status, error) => {
          var errorMsg = "Error sending message: " + error
          $status.addClass("alert alert-danger").text(errorMsg)
          Notification.addNotification({
            message: errorMsg,
            type: "error",
          })
        },
        complete: () => {
          $btn.prop("disabled", false).text("Send Message")
        },
      })
    })
  },
}))
