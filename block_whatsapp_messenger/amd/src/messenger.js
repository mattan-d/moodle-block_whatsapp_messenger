// This file is part of Moodle - http://moodle.org/

// Declare the define variable before using it
var define = window.define

define(["jquery", "core/ajax", "core/notification"], ($, Ajax, Notification) => {
  // Declare the M variable before using it
  var M = window.M

  return {
    init: (courseId) => {
      var form = $("#whatsapp-message-form")
      var statusDiv = $("#whatsapp-status")
      var sendBtn = $("#send-whatsapp-btn")

      form.on("submit", (e) => {
        e.preventDefault()

        var selectedStudents = $("#student-select").val()
        var message = $("#message-text").val().trim()

        // Validation
        if (!selectedStudents || selectedStudents.length === 0) {
          Notification.addNotification({
            message: M.util.get_string("selectstudentserror", "block_whatsapp_messenger"),
            type: "error",
          })
          return
        }

        if (message === "") {
          Notification.addNotification({
            message: M.util.get_string("messageempty", "block_whatsapp_messenger"),
            type: "error",
          })
          return
        }

        // Disable button and show loading
        sendBtn.prop("disabled", true).text("Sending...")
        statusDiv.html('<div class="alert alert-info">Sending messages...</div>')

        // Prepare student IDs
        var studentIds = selectedStudents.includes("all") ? "all" : selectedStudents.join(",")

        // Send AJAX request
        $.ajax({
          url: M.cfg.wwwroot + "/blocks/whatsapp_messenger/send_message.php",
          type: "POST",
          data: {
            courseid: courseId,
            students: studentIds,
            message: message,
            sesskey: M.cfg.sesskey,
          },
          dataType: "json",
          success: (response) => {
            if (response.success) {
              var msg = "Messages sent successfully! "
              msg += "Sent: " + response.sent
              if (response.failed > 0) {
                msg += ", Failed: " + response.failed
              }

              statusDiv.html('<div class="alert alert-success">' + msg + "</div>")

              if (response.errors && response.errors.length > 0) {
                var errorHtml = '<div class="alert alert-warning"><strong>Errors:</strong><ul>'
                response.errors.forEach((error) => {
                  errorHtml += "<li>" + error + "</li>"
                })
                errorHtml += "</ul></div>"
                statusDiv.append(errorHtml)
              }

              // Clear form
              $("#message-text").val("")
              $("#student-select").val([])
            } else {
              statusDiv.html('<div class="alert alert-danger">' + response.error + "</div>")
            }
          },
          error: () => {
            statusDiv.html('<div class="alert alert-danger">An error occurred while sending messages.</div>')
          },
          complete: () => {
            sendBtn.prop("disabled", false).text(M.util.get_string("sendmessage", "block_whatsapp_messenger"))
          },
        })
      })
    },
  }
})
