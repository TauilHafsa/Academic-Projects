**Advanced C Language: Socket and Data Structures**


**Objective:**

This project aims to develop two applications in C using sockets and data structures to manage connections, authentication, and interactions between a client and a server.


1. Client-Side Authentication:

   - The client must connect to the server socket.
   - It enters its login and requests a password.
   - If the password is incorrect, the client can retry up to 3 times. After that, the system locks.
   - If the password is correct, the user's profile (administrator or guest) is retrieved.


2. Menu for the Administrator:

   - Once logged in as an administrator, the menu should be displayed.
   - The administrator can enter information and send it to the server.
   - The server receives the data and stores it in a text file named contacts.txt.


3. Custom Menu for the Guest:

   - The server must be ready to receive client connections.
   - It verifies the authentication of clients (login, password).
   - If the user is an administrator, the administrator's menu is displayed.
   - Otherwise, the guest can only view and search contacts. The menu is customized based on the user's profile.


4. Contact Management:

   - The server must display all received requests.
   - It performs the requested processing by the client, including adding, modifying, deleting, and searching for contacts.


![Diagramme sans nom drawio](https://github.com/TauilHafsa/Academic-Projects/assets/150071317/71f48633-ff23-4b6e-a6fd-0b7a2cdb1fd9)
