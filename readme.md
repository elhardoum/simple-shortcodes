# Simple PHP Shortcodes
Implementation of shortcodes in PHP, WordPress-inspired

## Example Usage:

```php
// loading the class
if ( !class_exists('\App\Shortcodes') ) {
    require ( 'path/to/Shortcodes.php' );
}

// declare these functions so as to make usage more easier

function add_shortcode($tag, $callback) {
    return \App\Shortcodes::instance()->register($tag, $callback);
}

function do_shortcode($str) {
    return \App\Shortcodes::instance()->doShortcode($str);
}
```

Here's a raw we want to parse shortcodes in it:

```php
print ($welcome = 'Welcome to my world! click [login]here[/login] to login!');

# Welcome to my world! click [login]here[/login] to login!
```

Now we want to register the shortcode `login` and use `do_shortcode` to make it listen for shortcodes:

```php
add_shortcode('login', function($atts, $content){
    return sprintf(
        '<a href="%s">%s</a>',
        $login_url = '/login.php',
        $content
    ); 
});

print ($welcome = do_shortcode('Welcome to my world! click [login]here[/login] to login!'));

#Welcome to my world! click <a href="/login.php">here</a> to login!
```

## Shortcode with attributes:

You can also pass attributes to the shortcodes which you can use later in the shortcode callback. Here are couple example shortcodes with attributes:

- `[link rel="nofollow" /]`
- `[image src="pixel.gif" alt=image lazyload='true' /]`

Now if you debug the `$atts` in the shortcode callback, you'll find all of the shortcode attributes as an array:

```php
add_shortcode('image', function($atts, $content){
    print_r($atts);
});

echo do_shortcode('[image src="pixel.gif" alt=image lazyload=\'true\' /]');

# > ['src' => 'pixel.gif', 'alt' => 'image', 'lazyload' => 'true']
```