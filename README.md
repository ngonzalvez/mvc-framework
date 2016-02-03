## MVC Framework
This is a simple MVC framework for PHP programming language.

By all means, this is not meant to be used in production since this code is not being maintained. However, this may be useful for those who are learning programming to understand the basis of the MVC architecture.

If you have any doubts, please feel free to contact me at [n.gonzalvez@outlook.com](mailto:n.gonzalvez@outlook.com).


## Router
The router is one of the main parts of the framework. Once the HTTP request has been received, the router will handle that request and pass the control to the correct controller.

The requests determine which controller will be instantiated and which method will be called, as well as the parameters passed to it.

When the web browser requests the URL:

```
  http://domain.com/user/profile/kryz
```
The URL will be parsed (with the help of the parseURL helper) and interpreted as a request for the controller `User`, a call to the method `profile` with the first parameter value being `kryz`.

In fact, what will happen is that the URL will be parsed considering the next convention:
```
  http://domain.com/controller/method/firstParameter/secondParameter/thirdParameter/andSoOn
```

Basically what it will do is to consider the first fragment of the URL after the domain as the controller name, the second as the method name, and the others will be passed to the method as arguments.

## Controllers
The controller is the part of the framework that glues all of the others. It will generally load a model, ask for some data, receive the data and pass it to the view to be rendered. After that the result will be sent to the client to be rendered in the web browser.

A controller is just a class that extends the base class `Controller`. It will always implement a method named `index` that will be called if no other method is specified in the request.

A simple example of a controller is the next:

```php
class Welcome extends Controller
{
  public function index() {
    echo "Hello world!";
  }
}
```

This simple controller will print `Hello world!` when this URL is requested:
```
  http://domain.com/welcome
```

Notice that in the URL `welcome` is written in lowercase and the controller class name starts with a capital letter. In the URL it doesn't matter if the first letter is lowercase or uppercase, it will automatically capitalize the controller name before instantiating it.

Apart from the default method `index`, there may be (and probably there will be) more methods. The only requirement for them is to be **public**.

An example of adding a method to our controller would be:

```php
class Welcome extends Controller
{
  // Default method.
  public function index() {
    echo "Hello world!";
  }
    
  // Our new method.
  public function sayHi($name) {
    echo "Hi $name!";
  }
}
```

Now, performing an HTTP request to the URL `http://domain.com/welcome/sayHi/kryz` would output:

```
Hi kryz!
```

## Views
A view is simply a PHP file with HTML inside. The view should contain all the UI-related stuff and none of the logic. All views must be located inside of `/app/views/`.

To load a view (it should only be loaded from inside a controller) just write:

```php
class Example extends Controller
{
  public function index() {
    $this->load->view("SomeView");
  }
}
```
As explained before, there should be some file named `SomeView.php` in the `/app/views/` folder.

You can also pass some data to the view. You can do it by passing an associative array as a second argument.

```php
class Example extends Controller
{
  public function index() {
    $data = array(
        "title" => "Passing data to a view",
        "text"  => "This text came directly from the controller."
      );
        
    $this->load->view("SomeView", $data);
  }
}

```

Inside the view, every key in the associative array will be converted to a variable:

```html
<!DOCTYPE html>
<html>
<body>
  <h1><?=$title?></h1>
  <p><?=$text?></p>
</body>
</html>
```

Notice that `<?=` is used as a shorthand for `<?php echo`. This is only possible if `short_open_tag` is set to **On** in `php.ini`.

## Helpers
A helper is just a function that provides some useful behaviour. You can create your own helpers. To do so, you just have to create a file in the `/app/helpers/` folder with the name than your helper.

For example, let's say we are going to create a helper named `sayHi`, then we'll need to create the file `/app/helpers/sayHi.php`. Inside of the file we'll write a function with the same name than the file and put our desired behaviour in it. For this example, our helper will just say hi to the name passed to it as an argument.

```php
function sayHi($name) {
  echo "Hi $name!";
}
```
And that's it. We already have our helper. Now we can load it and use it inside of our controller.

```php
class Example extends Controller
{
  public function index() {
    // Loads the helper.
    $this->load->helper("sayHi");
        
    // And now we simply use it.
    sayHi("Kryz");
  } 
}
```

And it will print `Hi Kryz!` on the screen.

## Models
A model is where all the business logic is performed, providing the controller with all the data it needs. This is the part of the framework that handles the data storag and the logic bound to it.

For example, in a social website we could have one model for handling all the user-related data and logic, such as: login, registration, user profile, etc. And we could have another model for handling all the messages-related data and logic.

Creating a model is fairly easy: create a file with the name of the model located in `/app/models/` and create a class inside of it with the same name which extends the base class `Model`. 

Let's create a model named `DemoModel`:

```php
/**
 * File: DemoModel.php
 */
class DemoModel extends Model
{
  // Sample method.
  public function getSomeMessage() {
    $message = "This message comes from our DemoModel";
    return $message;
  }
}
```

Now that we already have our model, we can load it and use it inside of our controller this way:

```php
class Example extends Controller
{
  public function index() {
    $this->load->model("DemoModel", true);
    echo $this->DemoModel->getSomeMessage();
  }
}
```

Notice that we loaded our model by calling the `model` method in our loader, similarly to what we did when loading helpers. The model method will ask for, at least, two parameters: the name of the model and a boolean value indicating if autoconnection to the database is enabled.

Once it is loaded, it will be stored as a property inside of our controller with the same name than the model. We can change this behavior by passing a custom property name for storing our model as a third parameter to the `model` method in the loader.

```php
class Example extends Controller
{
  public function index() {
    $this->load->model("DemoModel", true, "myModel");
    echo $this->myModel->getSomeMessage();
  }
}
```

### Accessing the database
Until now, our message from our model has been hardcoded. Now we are going to change that.

Let's suppose we have a table named `messages` in a database that looks like this:

id | author     | content
---|----------------|----------
 1 | John Smith   | I like MVC frameworks!
 2 | John Doe   | Hello there!
 3 | Emma Smith   | This is awesome!
 
####SELECT
Now we can select all the rows from our table.
 
```php
class DemoModel extends Model
{
  // Prints all the messages in the DB.
  public function getSomeMessage() {
    $messages = $this->db->from("messages")
                         ->select()
                         ->result();                 
        
    foreach ($messages as $message) {
      echo "{$message->author} wrote: {$message->content}. <br/>";
    }
  }
}

// OUTPUT:
// John Smith wrote: I like MVC frameworks!
// John Doe wrote: Hello there!
// Emma Smith wrote: This is awesome!
```
Here, the result of the query is an array of rows where every row is represented by an object that has a property for every field in the table. If we want rows to be associative arrays instead of objects we only have to change `result()` for `result_array()`.

####WHERE
What if we don't want all the messages, what if we want just the message written by John Doe.

```php
class DemoModel extends Model
{
  // Prints on the screen the message written by John Doe
  public function getSomeMessage() {
    $message = $this->db->from("messages")
                        ->where("author", "John Doe")
                        ->select()
                        ->row();                 
        
    echo "{$message->author}'s message is '{$message->content}'";
  }
}

// OUTPUT:
// John Doe's message is 'Hello there!'
```

In the last example I introduced the `where()` method to specify a condition for our result. This method can be called with two arguments, as in the previous example, where the first argument is the name of the field and the second being the value of that field. But this method can also be called with one argument which would be an associative array where every key would be the name of the field and the value would be the value of that field.

Let's show that in an example.

```php
class DemoModel extends Model
{
  // Prints on the screen the message written by Emma Smith
  public function getSomeMessage() {
    $message = $this->db->from("messages")
                        ->where(array(
                              "author" => "Emma Smith",
                              "id" => 3
                            ))
                        ->select()
                        ->row();                 
        
    echo "{$message->author}'s message is '{$message->content}'";
  }
}

// OUTPUT:
// Emma Smith's message is 'This is awesome!'
```

####UPDATE
Now that we know how to select a specific row, we may want to update some values in it. For example, if we want to update John Doe's message we could do it this way:

```php
class DemoModel extends Model
{
  // Updates John Doe's message.
  public function updateMessage() {
    $message = $this->db->from("messages")
                        ->where("author", "John Doe")
                        ->update(array(
                                "content" => "This is a new message."
                            ));
  }
}
```
Then the updated values would be:

id | author       | content
---|----------------|----------
 1 | John Smith   | I like MVC frameworks!
 2 | John Doe     | This is a new message.
 3 | Emma Smith   | This is awesome!
 
 #### INSERT
 We can also insert new rows into our table with `$this->db->insert($table, $values)`.
 
 Inserting a new row can be done in two ways: by passing an associative array (fields => values) to `$this->db->insert()` as a second argument or by passing an array of just values to it.
 
 ```php
class DemoModel extends Model
{
  // Inserts two new messages
  public function insertMessage() {
    // Using an associative array.
    $this->db->insert("messages", array(
                        "author" => "Emma Smith",
                        "content" => "This is other message"
                      ));
                    
    // Using an array of values.
    $this->db->insert("messages", array('', 'Emma Smith', "Yet another message."));
  }
}
```
 The biggest difference is that with the associative array you don't have to specify the value for every field.
 
 #### DELETE
 We already saw we can create new rows, but we can also delete existing ones. To do so, we'll use `$this->db->delete()`.
 
 Let's delete all messages from John Smith.
  
 ```php
class DemoModel extends Model
{
  // Deletes John Smith's messages.
  public function deleteMessages() {
    $this->db->from("messages")
             ->where("author", "John Smith")
             ->delete();
  }
}
```

##Configuration
In the `/app/config/` folder you'll fild several INI files where you can configure your app settings.

The `App.ini` file is for general settings. Inside of it there is a `DEFAULT_CONTROLLER` value which controls which is the controller that will be loaded by default if no other is requested.

The `Database.ini` file is meant to contain all the connection data for the database. There you can set the DB username, password, server and database name.
