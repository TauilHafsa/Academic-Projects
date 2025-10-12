package server;

import java.util.regex.Pattern;
import javax.mail.*;
import javax.mail.internet.*;
import java.util.Properties;

public class EmailManager {
    private static final String EMAIL_REGEX =
            "^[a-zA-Z0-9_+&*-]+(?:\\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,7}$";

    public static boolean isValidEmail(String email) {
        return email != null && Pattern.compile(EMAIL_REGEX).matcher(email).matches();
    }

    public static void sendInvitationEmail(String toEmail) {
        // Sender's email ID and password (replace with your actual credentials)
        final String fromEmail = "your_email@gmail.com"; // Replace with your email
        final String password = "your_password"; // Replace with your password

        // Configure SMTP properties
        Properties props = new Properties();
        props.put("mail.smtp.host", "smtp.gmail.com"); // SMTP Host
        props.put("mail.smtp.port", "587"); // TLS Port
        props.put("mail.smtp.auth", "true"); // Authentication
        props.put("mail.smtp.starttls.enable", "true"); // Enable STARTTLS

        // Create a session with authentication
        Authenticator auth = new Authenticator() {
            protected PasswordAuthentication getPasswordAuthentication() {
                return new PasswordAuthentication(fromEmail, password);
            }
        };
        Session session = Session.getInstance(props, auth);

        try {
            // Create a MimeMessage object
            MimeMessage message = new MimeMessage(session);

            // Set the sender and recipient addresses
            message.setFrom(new InternetAddress(fromEmail));
            message.addRecipient(Message.RecipientType.TO, new InternetAddress(toEmail));

            // Set the subject and body of the email
            message.setSubject("Invitation to TalkBridge");
            message.setText("You have been invited to join TalkBridge! Click here to register: [Registration Link]"); // Replace with actual registration link

            // Send the email
            Transport.send(message);

            System.out.println("Invitation email sent successfully to: " + toEmail);

        } catch (MessagingException e) {
            System.err.println("Failed to send invitation email to: " + toEmail + " - " + e.getMessage());
            e.printStackTrace();
        }
    }
}
