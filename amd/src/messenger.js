// This file is part of Moodle - http://moodle.org/

var M = window.M // Declare the M variable

define(["jquery", "core/ajax", "core/notification"], ($, Ajax, Notification) => ({
  init: (courseid) => {
    console.log("[v0] WhatsApp Messenger initialized for course:", courseid)

    $("#whatsapp-message-form").on("submit", (e) => {
      console.log("[v0] Form submit event triggered")
      e.preventDefault()
      e.stopPropagation() // Added to prevent event bubbling

      var recipient = $("#recipient-select").val()
      var message = $("#message-text").val()

      console.log("[v0] Form data:", { recipient, message })

      if (!message.trim()) {
        console.log("[v0] Empty message, showing error")
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

      console.log("[v0] Sending AJAX request")

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
          console.log("[v0] AJAX success:", response)
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
            console.log("[v0] Response indicates failure:", response.error)
            $status.addClass("alert alert-danger").text(response.error)
            Notification.addNotification({
              message: response.error,
              type: "error",
            })
          }
        },
        error: (xhr, status, error) => {
          console.log("[v0] AJAX error:", { xhr, status, error })
          var errorMsg = "Error sending message: " + error
          $status.addClass("alert alert-danger").text(errorMsg)
          Notification.addNotification({
            message: errorMsg,
            type: "error",
          })
        },
        complete: () => {
          console.log("[v0] AJAX complete")
          $btn.prop("disabled", false).text("Send Message")
        },
      })

      return false
    })
  },
}))
