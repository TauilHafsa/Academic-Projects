package client.models;

public class User {
    private int id;
    private String email;
    private String firstName;
    private String lastName;
    private boolean online; // Keep field, but may not be used if status isn't tracked

    public User(int id, String email, String firstName, String lastName, boolean online) {
        this.id = id;
        this.email = email;
        this.firstName = firstName;
        this.lastName = lastName;
        this.online = online;
    }

    // Getters
    public int getId() { return id; }
    public String getEmail() { return email; }
    public String getFirstName() { return firstName; }
    public String getLastName() { return lastName; }
    public boolean isOnline() { return online; }

    // Method to get the full name
    public String getName() {
        // Handle potential null names gracefully
        String fName = (firstName != null) ? firstName : "";
        String lName = (lastName != null) ? lastName : "";
        return (fName + " " + lName).trim(); // Trim in case one name is null/empty
    }

    // Setters (optional, depending on usage)
    public void setOnline(boolean online) {
        this.online = online;
    }

    @Override
    public String toString() {
        return getName() + " (ID: " + id + ", Email: " + email + ")"; // More informative toString
    }
}
