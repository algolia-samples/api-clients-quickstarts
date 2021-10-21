import java.io.Serializable;
import java.util.Optional;

public class Contact implements Serializable {

  public Contact() {}

  public Contact(String objectID, String name, Optional<String> email) {
    this.objectID = objectID;
    this.name = name;
    this.email = email.orElse("");
  }

  public String toString() {
    return String.format("<Contact id=%s name=%s>", this.objectID, this.name);
  }

  public String getName() {
    return name;
  }

  public Contact setName(String name) {
    this.name = name;
    return this;
  }

  public String getEmail() {
    return email;
  }

  public Contact setEmail(String email) {
    this.email = email;
    return this;
  }

  public String getObjectID() {
    return objectID;
  }

  public String getQueryID() {
    return queryID;
  }

  public Contact setQueryID(String queryID) {
    this.queryID = queryID;
    return this;
  }

  private String name;
  private String email;
  private String queryID;
  private String objectID;
}