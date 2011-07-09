<?php
class LocatorStrategy {
    /**Returns an element whose class name contains the search value; compound class names are not permitted.*/
    const className="class name";

    /**Returns an element matching a CSS selector.*/
    const cssSelector="css selector";

    /**Returns an element whose ID attribute matches the search value.*/
    const id="id";

    /**Returns an element whose NAME attribute matches the search value.*/
    const name="name";

    /**Returns an anchor element whose visible text matches the search value.*/
    const linkText="link text";

    /**Returns an anchor element whose visible text partially matches the search value.*/
    const partialLinkText="partial link text";

    /**Returns an element whose tag name matches the search value.*/
    const tagName="tag name";

    /**Returns an element matching an XPath expression.*/
    const xpath="xpath";
}
?>
