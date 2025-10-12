package common;

public class Protocol {
    // Commandes
    public static final String LOGIN_CMD = "LOGIN";
    public static final String REGISTER_CMD = "REGISTER";
    public static final String GET_USER_BY_EMAIL = "GET_USER_BY_EMAIL";
    public static final String ADD_CONTACT_CMD = "ADD_CONTACT";
    public static final String DELETE_CONTACT_CMD = "DELETE_CONTACT";
    public static final String INVITE_CONTACT_CMD = "INVITE_CONTACT";
    public static final String GET_INVITATIONS_CMD = "GET_INVITATIONS";
    public static final String ACCEPT_INVITATION_CMD = "ACCEPT_INVITATION";
    public static final String DECLINE_INVITATION_CMD = "DECLINE_INVITATION";
    public static final String SEND_MESSAGE_CMD = "SEND_MESSAGE";
    public static final String GET_MESSAGES_CMD = "GET_MESSAGES";

    // Réponses
    public static final String SUCCESS_PREFIX = "SUCCESS:";
    public static final String ERROR_PREFIX = "ERROR:";

    // Séparateur pour les messages
    public static final String SEPARATOR = "%%";
    public static final String MESSAGE_SEPARATOR = "||";
    public static final String GET_USERNAME_CMD = "GET_USERNAME";
    public static final String CMD_GET_USER = "GET_USER";
    public static final String DOWNLOAD_FILE = "DOWNLOAD_FILE";
}
