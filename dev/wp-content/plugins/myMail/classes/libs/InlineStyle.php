<?php
/*this classes are adopted for MyMail */


/*
 * InlineStyle MIT License
 *
 * Copyright (c) 2012 Christiaan Baartse
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * Parses a html file and applies all embedded and external stylesheets inline
 */
class MyMailInlineStyle
{
    /**
     * @var \DOMDocument the HTML as DOMDocument
     */
    private $_dom;

    /**
     * @var \DOMXPath
     */
    private $_dom_xpath;

    /**
     * Prepare all the necessary objects
     *
     * @param string $html
     */
    public function __construct($html = '')
    {
        if ($html) {
            if (strlen($html) <= PHP_MAXPATHLEN && file_exists($html))
                $this->loadHTMLFile($html);
            else
                $this->loadHTML($html);
        }
    }

    /**
     * Load HTML file
     *
     * @param string $filename
     */
    public function loadHTMLFile($filename)
    {
        $this->loadHTML(file_get_contents($filename));
    }

    /**
     * Load HTML string (UTF-8 encoding assumed)
     *
     * @param string $html
     */
    public function loadHTML($html)
    {
        $this->_dom = new DOMDocument();
        $this->_dom->formatOutput = true;

        // strip illegal XML UTF-8 chars
        // remove all control characters except CR, LF and tab
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $html); // 00-09, 11-31, 127

        @$this->_dom->loadHTML($html);
        $this->_dom_xpath = new DOMXPath($this->_dom);
    }

    /**
     * Applies one or more stylesheets to the current document
     *
     * @param string $stylesheet
     * @return InlineStyle self
     */
    public function applyStylesheet($stylesheet)
    {
        $stylesheet = (array) $stylesheet;
        foreach($stylesheet as $ss) {
            $parsed = $this->parseStylesheet($ss);
            $parsed = $this->sortSelectorsOnSpecificity($parsed);
            foreach($parsed as $arr) {
                list($selector, $style) = $arr;
                $this->applyRule($selector, $style);
            }
        }

        return $this;
    }

    private function _getNodesForCssSelector($sel)
    {
        try {
            $xpathQuery = MyMailCssSelector::toXPath($sel);
            return $this->_dom_xpath->query($xpathQuery);
        }
        catch(MyMailParseException $e) {
            // ignore css rule parse exceptions
        }

        return array();
    }

    /**
     * Applies a style rule on the document
     * @param string $selector
     * @param string $style
     * @return InlineStyle self
     */
    public function applyRule($selector, $style)
    {
        if($selector) {
            $nodes = $this->_getNodesForCssSelector($selector);
            $style = $this->_styleToArray($style);

            foreach($nodes as $node) {
                $current = $node->hasAttribute("style") ?
                    $this->_styleToArray($node->getAttribute("style")) :
                    array();

                $current = $this->_mergeStyles($current, $style);
                $st = array();

                foreach($current as $prop => $val) {
                    $st[] = "{$prop}:{$val}";
                }

                $node->setAttribute("style", implode(";", $st));
            }
        }

        return $this;
    }

    /**
     * Returns the DOMDocument as html
     *
     * @return string the HTML
     */
    public function getHTML()
    {
        return $this->_dom->saveHTML();
    }

    /**
     * Recursively extracts the stylesheet nodes from the DOMNode
     *
     * @param \DOMNode $node leave empty to extract from the whole document
     * @param string $base The base URI for relative stylesheets
     * @return array the extracted stylesheets
     */
    public function extractStylesheets($node = null, $base = '')
    {
        if(null === $node) {
            $node = $this->_dom;
        }

        $stylesheets = array();

        if(strtolower($node->nodeName) === "style") {
            $stylesheets[] = $node->nodeValue;
            $node->parentNode->removeChild($node);
        }
        else if(strtolower($node->nodeName) === "link") {
            if($node->hasAttribute("href")) {
                $href = $node->getAttribute("href");

                if($base && false === strpos($href, "://")) {
                    $href = "{$base}/{$href}";
                }

                $ext = @file_get_contents($href);

                if($ext) {
                    $stylesheets[] = $ext;
                    $node->parentNode->removeChild($node);
                }
            }
        }

        if($node->hasChildNodes()) {
            foreach($node->childNodes as $child) {
                $stylesheets = array_merge($stylesheets,
                    $this->extractStylesheets($child, $base));
            }
        }

        return $stylesheets;
    }

    /**
     * Extracts the stylesheet nodes nodes specified by the xpath
     *
     * @param string $xpathQuery xpath query to the desired stylesheet
     * @return array the extracted stylesheets
     */
    public function extractStylesheetsWithXpath($xpathQuery)
    {
        $stylesheets = array();

        $nodes = $this->_dom_xpath->query($xpathQuery);
        foreach ($nodes as $node)
        {
            $stylesheets[] = $node->nodeValue;
            $node->parentNode->removeChild($node);
        }

        return $stylesheets;
    }

    /**
     * Parses a stylesheet to selectors and properties
     * @param string $stylesheet
     * @return array
     */
    public function parseStylesheet($stylesheet)
    {
        $parsed = array();
        $stylesheet = $this->_stripStylesheet($stylesheet);
        $stylesheet = trim(trim($stylesheet), "}");
        foreach(explode("}", $stylesheet) as $rule) {
            //Don't parse empty rules
        	if(!trim($rule))continue;
        	list($selector, $style) = explode("{", $rule, 2);
            foreach (explode(',', $selector) as $sel) {
                $parsed[] = array(trim($sel), trim(trim($style), ";"));
            }
        }

        return $parsed;
    }

    public function sortSelectorsOnSpecificity($parsed)
    {
        usort($parsed, array($this, 'sortOnSpecificity'));
        return $parsed;
    }

    private function sortOnSpecificity($a, $b)
    {
        $a = $this->getScoreForSelector($a[0]);
        $b = $this->getScoreForSelector($b[0]);

        foreach (range(0, 2) as $i) {
            if ($a[$i] !== $b[$i]) {
                return $a[$i] < $b[$i] ? -1 : 1;
            }
        }
        return 0;
    }

    public function getScoreForSelector($selector)
    {
        return array(
            preg_match_all('/#\w/i', $selector, $result), // ID's
            preg_match_all('/\.\w/i', $selector, $result), // Classes
            preg_match_all('/^\w|\ \w|\(\w|\:[^not]/i', $selector, $result) // Tags
        );
    }

    /**
     * Parses style properties to a array which can be merged by mergeStyles()
     * @param string $style
     * @return array
     */
    private function _styleToArray($style)
    {
        $styles = array();
        $style = trim(trim($style), ";");
        if($style) {
            foreach(explode(";", $style) as $props) {
                $props = trim(trim($props), ";");
                //Don't parse empty props
                if(!trim($props))continue;
                preg_match('#^([-a-z0-9\*]+):(.*)$#i', $props, $matches);
                list($match, $prop, $val) = $matches;
                $styles[$prop] = $val;
            }
        }

        return $styles;
    }

    /**
     * Merges two sets of style properties taking !important into account
     * @param array $styleA
     * @param array $styleB
     * @return array
     */
    private function _mergeStyles(array $styleA, array $styleB)
    {
        foreach($styleB as $prop => $val) {
            if(!isset($styleA[$prop])
                || substr(str_replace(" ", "", strtolower($styleA[$prop])), -10) !== "!important")
            {
                $styleA[$prop] = $val;
            }
        }

        return $styleA;
    }

    private function _stripStylesheet($s)
    {
        // strip comments
        $s = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','', $s);
        
        // strip keyframes rules
        $s = preg_replace('/@[-|keyframes].*?\{.*?\}[ \r\n]*\}/s', '', $s);

        return $s;
    }
}




/**
 * CssSelector is the main entry point of the component and can convert CSS
 * selectors to XPath expressions.
 *
 * $xpath = CssSelector::toXpath('h1.foo');
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class MyMailCssSelector
{
    /**
     * Translates a CSS expression to its XPath equivalent.
     * Optionally, a prefix can be added to the resulting XPath
     * expression with the $prefix parameter.
     *
     * @param mixed  $cssExpr The CSS expression.
     * @param string $prefix  An optional prefix for the XPath expression.
     *
     * @return string
     *
     * @throws MyMailParseException When got None for xpath expression
     *
     * @api
     */
    public static function toXPath($cssExpr, $prefix = 'descendant-or-self::')
    {
        if (is_string($cssExpr)) {
            if (!$cssExpr) {
                return $prefix.'*';
            }

            if (preg_match('#^\w+\s*$#u', $cssExpr, $match)) {
                return $prefix.trim($match[0]);
            }

            if (preg_match('~^(\w*)#(\w+)\s*$~u', $cssExpr, $match)) {
                return sprintf("%s%s[@id = '%s']", $prefix, $match[1] ? $match[1] : '*', $match[2]);
            }

            if (preg_match('#^(\w*)\.(\w+)\s*$#u', $cssExpr, $match)) {
                return sprintf("%s%s[contains(concat(' ', normalize-space(@class), ' '), ' %s ')]", $prefix, $match[1] ? $match[1] : '*', $match[2]);
            }

            $parser = new self();
            $cssExpr = $parser->parse($cssExpr);
        }

        $expr = $cssExpr->toXpath();

        // @codeCoverageIgnoreStart
        if (!$expr) {
            throw new MyMailParseException(sprintf('Got None for xpath expression from %s.', $cssExpr));
        }
        // @codeCoverageIgnoreEnd

        if ($prefix) {
            $expr->addPrefix($prefix);
        }

        return (string) $expr;
    }

    /**
     * Parses an expression and returns the Node object that represents
     * the parsed expression.
     *
     * @param string $string The expression to parse
     *
     * @return NodeInterface
     *
     * @throws Exception When tokenizer throws it while parsing
     */
    public function parse($string)
    {
        $tokenizer = new MyMailTokenizer();

        $stream = new MyMailTokenStream($tokenizer->tokenize($string), $string);

        try {
            return $this->parseSelectorGroup($stream);
        } catch (Exception $e) {
            $class = get_class($e);

            throw new $class(sprintf('%s at %s -> %s', $e->getMessage(), implode($stream->getUsed(), ''), $stream->peek()), 0, $e);
        }
    }

    /**
     * Parses a selector group contained in $stream and returns
     * the Node object that represents the expression.
     *
     * @param TokenStream $stream The stream to parse.
     *
     * @return NodeInterface
     */
    private function parseSelectorGroup($stream)
    {
        $result = array();
        while (true) {
            $result[] = $this->parseSelector($stream);
            if ($stream->peek() == ',') {
                $stream->next();
            } else {
                break;
            }
        }

        if (count($result) == 1) {
            return $result[0];
        }

        return new MyMailOrNode($result);
    }

    /**
     * Parses a selector contained in $stream and returns the Node
     * object that represents it.
     *
     * @param TokenStream $stream The stream containing the selector.
     *
     * @return NodeInterface
     *
     * @throws MyMailParseException When expected selector but got something else
     */
    private function parseSelector($stream)
    {
        $result = $this->parseSimpleSelector($stream);

        while (true) {
            $peek = $stream->peek();
            if (',' == $peek || null === $peek) {
                return $result;
            } elseif (in_array($peek, array('+', '>', '~'))) {
                // A combinator
                $combinator = (string) $stream->next();

                // Ignore optional whitespace after a combinator
                while (' ' == $stream->peek()) {
                    $stream->next();
                }
            } else {
                $combinator = ' ';
            }
            $consumed = count($stream->getUsed());
            $nextSelector = $this->parseSimpleSelector($stream);
            if ($consumed == count($stream->getUsed())) {
                throw new MyMailParseException(sprintf("Expected selector, got '%s'", $stream->peek()));
            }

            $result = new MyMailCombinedSelectorNode($result, $combinator, $nextSelector);
        }

        return $result;
    }

    /**
     * Parses a simple selector (the current token) from $stream and returns
     * the resulting Node object.
     *
     * @param TokenStream $stream The stream containing the selector.
     *
     * @return NodeInterface
     *
     * @throws MyMailParseException When expected symbol but got something else
     */
    private function parseSimpleSelector($stream)
    {
        $peek = $stream->peek();
        if ('*' != $peek && !$peek->isType('Symbol')) {
            $element = $namespace = '*';
        } else {
            $next = $stream->next();
            if ('*' != $next && !$next->isType('Symbol')) {
                throw new MyMailParseException(sprintf("Expected symbol, got '%s'", $next));
            }

            if ($stream->peek() == '|') {
                $namespace = $next;
                $stream->next();
                $element = $stream->next();
                if ('*' != $element && !$next->isType('Symbol')) {
                    throw new MyMailParseException(sprintf("Expected symbol, got '%s'", $next));
                }
            } else {
                $namespace = '*';
                $element = $next;
            }
        }

        $result = new MyMailElementNode($namespace, $element);
        $hasHash = false;
        while (true) {
            $peek = $stream->peek();
            if ('#' == $peek) {
                if ($hasHash) {
                    /* You can't have two hashes
                        (FIXME: is there some more general rule I'm missing?) */
                    // @codeCoverageIgnoreStart
                    break;
                    // @codeCoverageIgnoreEnd
                }
                $stream->next();
                $result = new MyMailHashNode($result, $stream->next());
                $hasHash = true;

                continue;
            } elseif ('.' == $peek) {
                $stream->next();
                $result = new MyMailClassNode($result, $stream->next());

                continue;
            } elseif ('[' == $peek) {
                $stream->next();
                $result = $this->parseAttrib($result, $stream);
                $next = $stream->next();
                if (']' != $next) {
                    throw new MyMailParseException(sprintf("] expected, got '%s'", $next));
                }

                continue;
            } elseif (':' == $peek || '::' == $peek) {
                $type = $stream->next();
                $ident = $stream->next();
                if (!$ident || !$ident->isType('Symbol')) {
                    throw new MyMailParseException(sprintf("Expected symbol, got '%s'", $ident));
                }

                if ($stream->peek() == '(') {
                    $stream->next();
                    $peek = $stream->peek();
                    if ($peek->isType('String')) {
                        $selector = $stream->next();
                    } elseif ($peek->isType('Symbol') && is_int($peek)) {
                        $selector = intval($stream->next());
                    } else {
                        // FIXME: parseSimpleSelector, or selector, or...?
                        $selector = $this->parseSimpleSelector($stream);
                    }
                    $next = $stream->next();
                    if (')' != $next) {
                        throw new MyMailParseException(sprintf("Expected ')', got '%s' and '%s'", $next, $selector));
                    }

                    $result = new MyMailFunctionNode($result, $type, $ident, $selector);
                } else {
                    $result = new MyMailPseudoNode($result, $type, $ident);
                }

                continue;
            } else {
                if (' ' == $peek) {
                    $stream->next();
                }

                break;
            }
            // FIXME: not sure what "negation" is
        }

        return $result;
    }

    /**
     * Parses an attribute from a selector contained in $stream and returns
     * the resulting AttribNode object.
     *
     * @param NodeInterface $selector The selector object whose attribute
     *                                      is to be parsed.
     * @param TokenStream $stream The container token stream.
     *
     * @return AttribNode
     *
     * @throws MyMailParseException When encountered unexpected selector
     */
    private function parseAttrib($selector, $stream)
    {
        $attrib = $stream->next();
        if ($stream->peek() == '|') {
            $namespace = $attrib;
            $stream->next();
            $attrib = $stream->next();
        } else {
            $namespace = '*';
        }

        if ($stream->peek() == ']') {
            return new MyMailAttribNode($selector, $namespace, $attrib, 'exists', null);
        }

        $op = $stream->next();
        if (!in_array($op, array('^=', '$=', '*=', '=', '~=', '|=', '!='))) {
            throw new MyMailParseException(sprintf("Operator expected, got '%s'", $op));
        }

        $value = $stream->next();
        if (!$value->isType('Symbol') && !$value->isType('String')) {
            throw new MyMailParseException(sprintf("Expected string or symbol, got '%s'", $value));
        }

        return new MyMailAttribNode($selector, $namespace, $attrib, $op, $value);
    }
}

class MyMailParseException extends Exception
{
}



/**
 * Token represents a CSS Selector token.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailToken
{
    private $type;
    private $value;
    private $position;

    /**
     * Constructor.
     *
     * @param string  $type     The type of this token.
     * @param mixed   $value    The value of this token.
     * @param integer $position The order of this token.
     */
    public function __construct($type, $value, $position)
    {
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
    }

    /**
     * Gets a string representation of this token.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Answers whether this token's type equals to $type.
     *
     * @param string $type The type to test against this token's one.
     *
     * @return Boolean
     */
    public function isType($type)
    {
        return $this->type == $type;
    }

    /**
     * Gets the position of this token.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}


/**
 * Tokenizer lexes a CSS Selector to tokens.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailTokenizer
{
    /**
     * Takes a CSS selector and returns an array holding the Tokens
     * it contains.
     *
     * @param string $s The selector to lex.
     *
     * @return array Token[]
     */
    public function tokenize($s)
    {
        if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2) {
            $mbEncoding = mb_internal_encoding();
            mb_internal_encoding('ASCII');
        }

        $tokens = array();
        $pos = 0;
        $s = preg_replace('#/\*.*?\*/#s', '', $s);

        while (true) {
            if (preg_match('#\s+#A', $s, $match, 0, $pos)) {
                $precedingWhitespacePos = $pos;
                $pos += strlen($match[0]);
            } else {
                $precedingWhitespacePos = 0;
            }

            if ($pos >= strlen($s)) {
                if (isset($mbEncoding)) {
                    mb_internal_encoding($mbEncoding);
                }

                return $tokens;
            }

            if (preg_match('#[+-]?\d*n(?:[+-]\d+)?#A', $s, $match, 0, $pos) && 'n' !== $match[0]) {
                $sym = substr($s, $pos, strlen($match[0]));
                $tokens[] = new MyMailToken('Symbol', $sym, $pos);
                $pos += strlen($match[0]);

                continue;
            }

            $c = $s[$pos];
            $c2 = substr($s, $pos, 2);
            if (in_array($c2, array('~=', '|=', '^=', '$=', '*=', '::', '!='))) {
                $tokens[] = new MyMailToken('Token', $c2, $pos);
                $pos += 2;

                continue;
            }

            if (in_array($c, array('>', '+', '~', ',', '.', '*', '=', '[', ']', '(', ')', '|', ':', '#'))) {
                if (in_array($c, array('.', '#', '[')) && $precedingWhitespacePos > 0) {
                    $tokens[] = new MyMailToken('Token', ' ', $precedingWhitespacePos);
                }
                $tokens[] = new MyMailToken('Token', $c, $pos);
                ++$pos;

                continue;
            }

            if ('"' === $c || "'" === $c) {
                // Quoted string
                $oldPos = $pos;
                list($sym, $pos) = $this->tokenizeEscapedString($s, $pos);

                $tokens[] = new MyMailToken('String', $sym, $oldPos);

                continue;
            }

            $oldPos = $pos;
            list($sym, $pos) = $this->tokenizeSymbol($s, $pos);

            $tokens[] = new MyMailToken('Symbol', $sym, $oldPos);

            continue;
        }
    }

    /**
     * Tokenizes a quoted string (i.e. 'A string quoted with \' characters'),
     * and returns an array holding the unquoted string contained by $s and
     * the new position from which tokenizing should take over.
     *
     * @param string  $s   The selector string containing the quoted string.
     * @param integer $pos The starting position for the quoted string.
     *
     * @return array
     *
     * @throws MyMailParseException When expected closing is not found
     */
    private function tokenizeEscapedString($s, $pos)
    {
        $quote = $s[$pos];

        $pos = $pos + 1;
        $start = $pos;
        while (true) {
            $next = strpos($s, $quote, $pos);
            if (false === $next) {
                throw new MyMailParseException(sprintf('Expected closing %s for string in: %s', $quote, substr($s, $start)));
            }

            $result = substr($s, $start, $next - $start);
            if (strlen($result) > 0 && '\\' === $result[strlen($result) - 1]) {
                // next quote character is escaped
                $pos = $next + 1;
                continue;
            }

            if (false !== strpos($result, '\\')) {
                $result = $this->unescapeStringLiteral($result);
            }

            return array($result, $next + 1);
        }
    }

    /**
     * Unescapes a string literal and returns the unescaped string.
     *
     * @param string $literal The string literal to unescape.
     *
     * @return string
     *
     * @throws MyMailParseException When invalid escape sequence is found
     */
    private function unescapeStringLiteral($literal)
    {
        return preg_replace_callback('#(\\\\(?:[A-Fa-f0-9]{1,6}(?:\r\n|\s)?|[^A-Fa-f0-9]))#', array( $this, 'unescapeStringLiteralCallback' ), $literal);
    }
    private function unescapeStringLiteralCallback($matches)
    {
        if ($matches[0][0] == '\\' && strlen($matches[0]) > 1) {
            $matches[0] = substr($matches[0], 1);
            if (in_array($matches[0][0], array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'a', 'b', 'c', 'd', 'e', 'f'))) {
                return chr(trim($matches[0]));
            }
        } else {
            throw new MyMailParseException(sprintf('Invalid escape sequence %s in string %s', $matches[0], $literal));
        }
    }

    /**
     * Lexes selector $s and returns an array holding the name of the symbol
     * contained in it and the new position from which tokenizing should take
     * over.
     *
     * @param string  $s   The selector string.
     * @param integer $pos The position in $s at which the symbol starts.
     *
     * @return array
     *
     * @throws MyMailParseException When Unexpected symbol is found
     */
    private function tokenizeSymbol($s, $pos)
    {
        $start = $pos;

        if (!preg_match('#[^\w\-]#', $s, $match, PREG_OFFSET_CAPTURE, $pos)) {
            // Goes to end of s
            return array(substr($s, $start), strlen($s));
        }

        $matchStart = $match[0][1];

        if ($matchStart == $pos) {
            throw new MyMailParseException(sprintf('Unexpected symbol: %s at %s', $s[$pos], $pos));
        }

        $result = substr($s, $start, $matchStart - $start);
        $pos = $matchStart;

        return array($result, $pos);
    }
}


/**
 * TokenStream represents a stream of CSS Selector tokens.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailTokenStream
{
    private $used;
    private $tokens;
    private $source;
    private $peeked;
    private $peeking;

    /**
     * Constructor.
     *
     * @param array $tokens The tokens that make the stream.
     * @param mixed $source The source of the stream.
     */
    public function __construct($tokens, $source = null)
    {
        $this->used = array();
        $this->tokens = $tokens;
        $this->source = $source;
        $this->peeked = null;
        $this->peeking = false;
    }

    /**
     * Gets the tokens that have already been visited in this stream.
     *
     * @return array
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Gets the next token in the stream or null if there is none.
     * Note that if this stream was set to be peeking its behavior
     * will be restored to not peeking after this operation.
     *
     * @return mixed
     */
    public function next()
    {
        if ($this->peeking) {
            $this->peeking = false;
            $this->used[] = $this->peeked;

            return $this->peeked;
        }

        if (!count($this->tokens)) {
            return null;
        }

        $next = array_shift($this->tokens);
        $this->used[] = $next;

        return $next;
    }

    /**
     * Peeks for the next token in this stream. This means that the next token
     * will be returned but it won't be considered as used (visited) until the
     * next() method is invoked.
     * If there are no remaining tokens null will be returned.
     *
     * @see next()
     *
     * @return mixed
     */
    public function peek()
    {
        if (!$this->peeking) {
            if (!count($this->tokens)) {
                return null;
            }

            $this->peeked = array_shift($this->tokens);

            $this->peeking = true;
        }

        return $this->peeked;
    }
}

/**
 * AttribNode represents a "selector[namespace|attrib operator value]" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailAttribNode implements NodeInterface
{
    protected $selector;
    protected $namespace;
    protected $attrib;
    protected $operator;
    protected $value;

    /**
     * Constructor.
     *
     * @param NodeInterface $selector  The XPath selector
     * @param string        $namespace The namespace
     * @param string        $attrib    The attribute
     * @param string        $operator  The operator
     * @param string        $value     The value
     */
    public function __construct($selector, $namespace, $attrib, $operator, $value)
    {
        $this->selector = $selector;
        $this->namespace = $namespace;
        $this->attrib = $attrib;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        if ($this->operator == 'exists') {
            return sprintf('%s[%s[%s]]', __CLASS__, $this->selector, $this->formatAttrib());
        }

        return sprintf('%s[%s[%s %s %s]]', __CLASS__, $this->selector, $this->formatAttrib(), $this->operator, $this->value);
    }

    /**
     * {@inheritDoc}
     */
    public function toXpath()
    {
        $path = $this->selector->toXpath();
        $attrib = $this->xpathAttrib();
        $value = $this->value;
        if ($this->operator == 'exists') {
            $path->addCondition($attrib);
        } elseif ($this->operator == '=') {
            $path->addCondition(sprintf('%s = %s', $attrib, MyMailXPathExpr::xpathLiteral($value)));
        } elseif ($this->operator == '!=') {
            // FIXME: this seems like a weird hack...
            if ($value) {
                $path->addCondition(sprintf('not(%s) or %s != %s', $attrib, $attrib, MyMailXPathExpr::xpathLiteral($value)));
            } else {
                $path->addCondition(sprintf('%s != %s', $attrib, MyMailXPathExpr::xpathLiteral($value)));
            }
            // path.addCondition('%s != %s' % (attrib, xpathLiteral(value)))
        } elseif ($this->operator == '~=') {
            $path->addCondition(sprintf("contains(concat(' ', normalize-space(%s), ' '), %s)", $attrib, MyMailXPathExpr::xpathLiteral(' '.$value.' ')));
        } elseif ($this->operator == '|=') {
            // Weird, but true...
            $path->addCondition(sprintf('%s = %s or starts-with(%s, %s)', $attrib, MyMailXPathExpr::xpathLiteral($value), $attrib, XPathExpr::xpathLiteral($value.'-')));
        } elseif ($this->operator == '^=') {
            $path->addCondition(sprintf('starts-with(%s, %s)', $attrib, MyMailXPathExpr::xpathLiteral($value)));
        } elseif ($this->operator == '$=') {
            // Oddly there is a starts-with in XPath 1.0, but not ends-with
            $path->addCondition(sprintf('substring(%s, string-length(%s)-%s) = %s', $attrib, $attrib, strlen($value) - 1, MyMailXPathExpr::xpathLiteral($value)));
        } elseif ($this->operator == '*=') {
            // FIXME: case sensitive?
            $path->addCondition(sprintf('contains(%s, %s)', $attrib, MyMailXPathExpr::xpathLiteral($value)));
        } else {
            throw new MyMailParseException(sprintf('Unknown operator: %s', $this->operator));
        }

        return $path;
    }

    /**
     * Returns the XPath Attribute
     *
     * @return string The XPath attribute
     */
    protected function xpathAttrib()
    {
        // FIXME: if attrib is *?
        if ($this->namespace == '*') {
            return '@'.$this->attrib;
        }

        return sprintf('@%s:%s', $this->namespace, $this->attrib);
    }

    /**
     * Returns a formatted attribute
     *
     * @return string The formatted attribute
     */
    protected function formatAttrib()
    {
        if ($this->namespace == '*') {
            return $this->attrib;
        }

        return sprintf('%s|%s', $this->namespace, $this->attrib);
    }
}


/**
 * CombinedSelectorNode represents a combinator node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailCombinedSelectorNode implements NodeInterface
{
    protected static $methodMapping = array(
        ' ' => 'descendant',
        '>' => 'child',
        '+' => 'direct_adjacent',
        '~' => 'indirect_adjacent',
    );

    protected $selector;
    protected $combinator;
    protected $subselector;

    /**
     * The constructor.
     *
     * @param NodeInterface $selector    The XPath selector
     * @param string        $combinator  The combinator
     * @param NodeInterface $subselector The sub XPath selector
     */
    public function __construct($selector, $combinator, $subselector)
    {
        $this->selector = $selector;
        $this->combinator = $combinator;
        $this->subselector = $subselector;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        $comb = $this->combinator == ' ' ? '<followed>' : $this->combinator;

        return sprintf('%s[%s %s %s]', __CLASS__, $this->selector, $comb, $this->subselector);
    }

    /**
     * {@inheritDoc}
     * @throws MyMailParseException When unknown combinator is found
     */
    public function toXpath()
    {
        if (!isset(self::$methodMapping[$this->combinator])) {
            throw new MyMailParseException(sprintf('Unknown combinator: %s', $this->combinator));
        }

        $method = '_xpath_'.self::$methodMapping[$this->combinator];
        $path = $this->selector->toXpath();

        return $this->$method($path, $this->subselector);
    }

    /**
     * Joins a NodeInterface into the XPath of this object.
     *
     * @param XPathExpr     $xpath The XPath expression for this object
     * @param NodeInterface $sub   The NodeInterface object to add
     *
     * @return XPathExpr An XPath instance
     */
    protected function _xpath_descendant($xpath, $sub)
    {
        // when sub is a descendant in any way of xpath
        $xpath->join('/descendant::', $sub->toXpath());

        return $xpath;
    }

    /**
     * Joins a NodeInterface as a child of this object.
     *
     * @param XPathExpr     $xpath The parent XPath expression
     * @param NodeInterface $sub   The NodeInterface object to add
     *
     * @return XPathExpr An XPath instance
     */
    protected function _xpath_child($xpath, $sub)
    {
        // when sub is an immediate child of xpath
        $xpath->join('/', $sub->toXpath());

        return $xpath;
    }

    /**
     * Joins an XPath expression as an adjacent of another.
     *
     * @param XPathExpr     $xpath The parent XPath expression
     * @param NodeInterface $sub   The adjacent XPath expression
     *
     * @return XPathExpr An XPath instance
     */
    protected function _xpath_direct_adjacent($xpath, $sub)
    {
        // when sub immediately follows xpath
        $xpath->join('/following-sibling::', $sub->toXpath());
        $xpath->addNameTest();
        $xpath->addCondition('position() = 1');

        return $xpath;
    }

    /**
     * Joins an XPath expression as an indirect adjacent of another.
     *
     * @param XPathExpr     $xpath The parent XPath expression
     * @param NodeInterface $sub   The indirect adjacent NodeInterface object
     *
     * @return XPathExpr An XPath instance
     */
    protected function _xpath_indirect_adjacent($xpath, $sub)
    {
        // when sub comes somewhere after xpath as a sibling
        $xpath->join('/following-sibling::', $sub->toXpath());

        return $xpath;
    }
}


/**
 * ClassNode represents a "selector.className" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailClassNode implements NodeInterface
{
    protected $selector;
    protected $className;

    /**
     * The constructor.
     *
     * @param NodeInterface $selector  The XPath Selector
     * @param string        $className The class name
     */
    public function __construct($selector, $className)
    {
        $this->selector = $selector;
        $this->className = $className;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%s[%s.%s]', __CLASS__, $this->selector, $this->className);
    }

    /**
     * {@inheritDoc}
     */
    public function toXpath()
    {
        $selXpath = $this->selector->toXpath();
        $selXpath->addCondition(sprintf("contains(concat(' ', normalize-space(@class), ' '), %s)", MyMailXPathExpr::xpathLiteral(' '.$this->className.' ')));

        return $selXpath;
    }
}


/**
 * ElementNode represents a "namespace|element" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailElementNode implements NodeInterface
{
    protected $namespace;
    protected $element;

    /**
     * Constructor.
     *
     * @param string $namespace Namespace
     * @param string $element   Element
     */
    public function __construct($namespace, $element)
    {
        $this->namespace = $namespace;
        $this->element = $element;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%s[%s]', __CLASS__, $this->formatElement());
    }

    /**
     * Formats the element into a string.
     *
     * @return string Element as an XPath string
     */
    public function formatElement()
    {
        if ($this->namespace == '*') {
            return $this->element;
        }

        return sprintf('%s|%s', $this->namespace, $this->element);
    }

    /**
     * {@inheritDoc}
     */
    public function toXpath()
    {
        if ($this->namespace == '*') {
            $el = strtolower($this->element);
        } else {
            // FIXME: Should we lowercase here?
            $el = sprintf('%s:%s', $this->namespace, $this->element);
        }

        return new MyMailXPathExpr(null, null, $el);
    }
}


/**
 * FunctionNode represents a "selector:name(expr)" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailFunctionNode implements NodeInterface
{
    protected static $unsupported = array('target', 'lang', 'enabled', 'disabled');

    protected $selector;
    protected $type;
    protected $name;
    protected $expr;

    /**
     * Constructor.
     *
     * @param NodeInterface $selector The XPath expression
     * @param string        $type
     * @param string        $name
     * @param XPathExpr     $expr
     */
    public function __construct($selector, $type, $name, $expr)
    {
        $this->selector = $selector;
        $this->type = $type;
        $this->name = $name;
        $this->expr = $expr;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%s[%s%s%s(%s)]', __CLASS__, $this->selector, $this->type, $this->name, $this->expr);
    }

    /**
     * {@inheritDoc}
     * @throws MyMailParseException When unsupported or unknown pseudo-class is found
     */
    public function toXpath()
    {
        $selPath = $this->selector->toXpath();
        if (in_array($this->name, self::$unsupported)) {
            throw new MyMailParseException(sprintf('The pseudo-class %s is not supported', $this->name));
        }
        $method = '_xpath_'.str_replace('-', '_', $this->name);
        if (!method_exists($this, $method)) {
            throw new MyMailParseException(sprintf('The pseudo-class %s is unknown', $this->name));
        }

        return $this->$method($selPath, $this->expr);
    }

    /**
     * undocumented function
     *
     * @param XPathExpr $xpath
     * @param mixed     $expr
     * @param Boolean   $last
     * @param Boolean   $addNameTest
     *
     * @return XPathExpr
     */
    protected function _xpath_nth_child($xpath, $expr, $last = false, $addNameTest = true)
    {
        list($a, $b) = $this->parseSeries($expr);
        if (!$a && !$b && !$last) {
            // a=0 means nothing is returned...
            $xpath->addCondition('false() and position() = 0');

            return $xpath;
        }

        if ($addNameTest) {
            $xpath->addNameTest();
        }

        $xpath->addStarPrefix();
        if ($a == 0) {
            if ($last) {
                $b = sprintf('last() - %s', $b);
            }
            $xpath->addCondition(sprintf('position() = %s', $b));

            return $xpath;
        }

        if ($last) {
            // FIXME: I'm not sure if this is right
            $a = -$a;
            $b = -$b;
        }

        if ($b > 0) {
            $bNeg = -$b;
        } else {
            $bNeg = sprintf('+%s', -$b);
        }

        if ($a != 1) {
            $expr = array(sprintf('(position() %s) mod %s = 0', $bNeg, $a));
        } else {
            $expr = array();
        }

        if ($b >= 0) {
            $expr[] = sprintf('position() >= %s', $b);
        } elseif ($b < 0 && $last) {
            $expr[] = sprintf('position() < (last() %s)', $b);
        }
        $expr = implode($expr, ' and ');

        if ($expr) {
            $xpath->addCondition($expr);
        }

        return $xpath;
        /* FIXME: handle an+b, odd, even
             an+b means every-a, plus b, e.g., 2n+1 means odd
             0n+b means b
             n+0 means a=1, i.e., all elements
             an means every a elements, i.e., 2n means even
             -n means -1n
             -1n+6 means elements 6 and previous */
    }

    /**
     * undocumented function
     *
     * @param XPathExpr $xpath
     * @param XPathExpr $expr
     *
     * @return XPathExpr
     */
    protected function _xpath_nth_last_child($xpath, $expr)
    {
        return $this->_xpath_nth_child($xpath, $expr, true);
    }

    /**
     * undocumented function
     *
     * @param XPathExpr $xpath
     * @param XPathExpr $expr
     *
     * @return XPathExpr
     *
     * @throws MyMailParseException
     */
    protected function _xpath_nth_of_type($xpath, $expr)
    {
        if ($xpath->getElement() == '*') {
            throw new MyMailParseException('*:nth-of-type() is not implemented');
        }

        return $this->_xpath_nth_child($xpath, $expr, false, false);
    }

    /**
     * undocumented function
     *
     * @param XPathExpr $xpath
     * @param XPathExpr $expr
     *
     * @return XPathExpr
     */
    protected function _xpath_nth_last_of_type($xpath, $expr)
    {
        return $this->_xpath_nth_child($xpath, $expr, true, false);
    }

    /**
     * undocumented function
     *
     * @param XPathExpr $xpath
     * @param XPathExpr $expr
     *
     * @return XPathExpr
     */
    protected function _xpath_contains($xpath, $expr)
    {
        // text content, minus tags, must contain expr
        if ($expr instanceof ElementNode) {
            $expr = $expr->formatElement();
        }

        // FIXME: lower-case is only available with XPath 2
        //$xpath->addCondition(sprintf('contains(lower-case(string(.)), %s)', XPathExpr::xpathLiteral(strtolower($expr))));
        $xpath->addCondition(sprintf('contains(string(.), %s)', MyMailXPathExpr::xpathLiteral($expr)));

        // FIXME: Currently case insensitive matching doesn't seem to be happening
        return $xpath;
    }

    /**
     * undocumented function
     *
     * @param XPathExpr $xpath
     * @param XPathExpr $expr
     *
     * @return XPathExpr
     */
    protected function _xpath_not($xpath, $expr)
    {
        // everything for which not expr applies
        $expr = $expr->toXpath();
        $cond = $expr->getCondition();
        // FIXME: should I do something about element_path?
        $xpath->addCondition(sprintf('not(%s)', $cond));

        return $xpath;
    }

    /**
     * Parses things like '1n+2', or 'an+b' generally, returning (a, b)
     *
     * @param mixed $s
     *
     * @return array
     */
    protected function parseSeries($s)
    {
        if ($s instanceof ElementNode) {
            $s = $s->formatElement();
        }

        if (!$s || '*' == $s) {
            // Happens when there's nothing, which the CSS parser thinks of as *
            return array(0, 0);
        }

        if ('odd' == $s) {
            return array(2, 1);
        }

        if ('even' == $s) {
            return array(2, 0);
        }

        if ('n' == $s) {
            return array(1, 0);
        }

        if (false === strpos($s, 'n')) {
            // Just a b
            return array(0, intval((string) $s));
        }

        list($a, $b) = explode('n', $s);
        if (!$a) {
            $a = 1;
        } elseif ('-' == $a || '+' == $a) {
            $a = intval($a.'1');
        } else {
            $a = intval($a);
        }

        if (!$b) {
            $b = 0;
        } elseif ('-' == $b || '+' == $b) {
            $b = intval($b.'1');
        } else {
            $b = intval($b);
        }

        return array($a, $b);
    }
}


/**
 * HashNode represents a "selector#id" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailHashNode implements NodeInterface
{
    protected $selector;
    protected $id;

    /**
     * Constructor.
     *
     * @param NodeInterface $selector The NodeInterface object
     * @param string        $id       The ID
     */
    public function __construct($selector, $id)
    {
        $this->selector = $selector;
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%s[%s#%s]', __CLASS__, $this->selector, $this->id);
    }

    /**
     * {@inheritDoc}
     */
    public function toXpath()
    {
        $path = $this->selector->toXpath();
        $path->addCondition(sprintf('@id = %s', MyMailXPathExpr::xpathLiteral($this->id)));

        return $path;
    }
}


/**
 * ClassNode represents a "selector.className" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface NodeInterface
{
    /**
     * Returns a string representation of the object.
     *
     * @return string The string representation
     */
    public function __toString();

    /**
     * @return XPathExpr The XPath expression
     *
     * @throws MyMailParseException When unknown operator is found
     */
    public function toXpath();
}


/**
 * OrNode represents a "Or" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailOrNode implements NodeInterface
{
    /**
     * @var NodeInterface[]
     */
    protected $items;

    /**
     * Constructor.
     *
     * @param NodeInterface[] $items An array of NodeInterface objects
     */
    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%s(%s)', __CLASS__, $this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function toXpath()
    {
        $paths = array();
        foreach ($this->items as $item) {
            $paths[] = $item->toXpath();
        }

        return new MyMailXPathExprOr($paths);
    }
}


/**
 * PseudoNode represents a "selector:ident" node.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailPseudoNode implements NodeInterface
{
    protected static $unsupported = array(
        'indeterminate', 'first-line', 'first-letter',
        'selection', 'before', 'after', 'link', 'visited',
        'active', 'focus', 'hover',
    );

    protected $element;
    protected $type;
    protected $ident;

    /**
     * Constructor.
     *
     * @param NodeInterface $element The NodeInterface element
     * @param string        $type    Node type
     * @param string        $ident   The ident
     *
     * @throws MyMailParseException When incorrect PseudoNode type is given
     */
    public function __construct($element, $type, $ident)
    {
        $this->element = $element;

        if (!in_array($type, array(':', '::'))) {
            throw new MyMailParseException(sprintf('The PseudoNode type can only be : or :: (%s given).', $type));
        }

        $this->type = $type;
        $this->ident = $ident;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return sprintf('%s[%s%s%s]', __CLASS__, $this->element, $this->type, $this->ident);
    }

    /**
     * {@inheritDoc}
     * @throws MyMailParseException When unsupported or unknown pseudo-class is found
     */
    public function toXpath()
    {
        $elXpath = $this->element->toXpath();

        if (in_array($this->ident, self::$unsupported)) {
            throw new MyMailParseException(sprintf('The pseudo-class %s is unsupported', $this->ident));
        }
        $method = 'xpath_'.str_replace('-', '_', $this->ident);
        if (!method_exists($this, $method)) {
            throw new MyMailParseException(sprintf('The pseudo-class %s is unknown', $this->ident));
        }

        return $this->$method($elXpath);
    }

    /**
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified XPath expression
     */
    protected function xpath_checked($xpath)
    {
        // FIXME: is this really all the elements?
        $xpath->addCondition("(@selected or @checked) and (name(.) = 'input' or name(.) = 'option')");

        return $xpath;
    }

    /**
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified XPath expression
     *
     * @throws MyMailParseException If this element is the root element
     */
    protected function xpath_root($xpath)
    {
        // if this element is the root element
        throw new MyMailParseException();
    }

    /**
     * Marks this XPath expression as the first child.
     *
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified expression
     */
    protected function xpath_first_child($xpath)
    {
        $xpath->addStarPrefix();
        $xpath->addNameTest();
        $xpath->addCondition('position() = 1');

        return $xpath;
    }

    /**
     * Sets the XPath  to be the last child.
     *
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified expression
     */
    protected function xpath_last_child($xpath)
    {
        $xpath->addStarPrefix();
        $xpath->addNameTest();
        $xpath->addCondition('position() = last()');

        return $xpath;
    }

    /**
     * Sets the XPath expression to be the first of type.
     *
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified expression
     *
     * @throws MyMailParseException
     */
    protected function xpath_first_of_type($xpath)
    {
        if ($xpath->getElement() == '*') {
            throw new MyMailParseException('*:first-of-type is not implemented');
        }
        $xpath->addStarPrefix();
        $xpath->addCondition('position() = 1');

        return $xpath;
    }

    /**
     * Sets the XPath expression to be the last of type.
     *
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified expression
     *
     * @throws MyMailParseException Because *:last-of-type is not implemented
     */
    protected function xpath_last_of_type($xpath)
    {
        if ($xpath->getElement() == '*') {
            throw new MyMailParseException('*:last-of-type is not implemented');
        }
        $xpath->addStarPrefix();
        $xpath->addCondition('position() = last()');

        return $xpath;
    }

    /**
     * Sets the XPath expression to be the only child.
     *
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified expression
     */
    protected function xpath_only_child($xpath)
    {
        $xpath->addNameTest();
        $xpath->addStarPrefix();
        $xpath->addCondition('last() = 1');

        return $xpath;
    }

    /**
     * Sets the XPath expression to be only of type.
     *
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified expression
     *
     * @throws MyMailParseException Because *:only-of-type is not implemented
     */
    protected function xpath_only_of_type($xpath)
    {
        if ($xpath->getElement() == '*') {
            throw new MyMailParseException('*:only-of-type is not implemented');
        }
        $xpath->addCondition('last() = 1');

        return $xpath;
    }

    /**
     * undocumented function
     *
     * @param XPathExpr $xpath The XPath expression
     *
     * @return XPathExpr The modified expression
     */
    protected function xpath_empty($xpath)
    {
        $xpath->addCondition('not(*) and not(normalize-space())');

        return $xpath;
    }
}



/**
 * XPathExpr represents an XPath expression.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailXPathExpr
{
    private $prefix;
    private $path;
    private $element;
    private $condition;
    private $starPrefix;

    /**
     * Constructor.
     *
     * @param string  $prefix     Prefix for the XPath expression.
     * @param string  $path       Actual path of the expression.
     * @param string  $element    The element in the expression.
     * @param string  $condition  A condition for the expression.
     * @param Boolean $starPrefix Indicates whether to use a star prefix.
     */
    public function __construct($prefix = null, $path = null, $element = '*', $condition = null, $starPrefix = false)
    {
        $this->prefix = $prefix;
        $this->path = $path;
        $this->element = $element;
        $this->condition = $condition;
        $this->starPrefix = $starPrefix;
    }

    /**
     * Gets the prefix of this XPath expression.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Gets the path of this XPath expression.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Answers whether this XPath expression has a star prefix.
     *
     * @return Boolean
     */
    public function hasStarPrefix()
    {
        return $this->starPrefix;
    }

    /**
     * Gets the element of this XPath expression.
     *
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Gets the condition of this XPath expression.
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Gets a string representation for this XPath expression.
     *
     * @return string
     */
    public function __toString()
    {
        $path = '';
        if (null !== $this->prefix) {
            $path .= $this->prefix;
        }

        if (null !== $this->path) {
            $path .= $this->path;
        }

        $path .= $this->element;

        if ($this->condition) {
            $path .= sprintf('[%s]', $this->condition);
        }

        return $path;
    }

    /**
     * Adds a condition to this XPath expression.
     * Any pre-existent condition will be ANDed to it.
     *
     * @param string $condition The condition to add.
     */
    public function addCondition($condition)
    {
        if ($this->condition) {
            $this->condition = sprintf('%s and (%s)', $this->condition, $condition);
        } else {
            $this->condition = $condition;
        }
    }

    /**
     * Adds a prefix to this XPath expression.
     * It will be prepended to any pre-existent prefixes.
     *
     * @param string $prefix The prefix to add.
     */
    public function addPrefix($prefix)
    {
        if ($this->prefix) {
            $this->prefix = $prefix.$this->prefix;
        } else {
            $this->prefix = $prefix;
        }
    }

    /**
     * Adds a condition to this XPath expression using the name of the element
     * as the desired value.
     * This method resets the element to '*'.
     */
    public function addNameTest()
    {
        if ($this->element == '*') {
            // We weren't doing a test anyway
            return;
        }

        $this->addCondition(sprintf('name() = %s', XPathExpr::xpathLiteral($this->element)));
        $this->element = '*';
    }

    /**
     * Adds a star prefix to this XPath expression.
     * This method will prepend a '*' to the path and set the star prefix flag
     * to true.
     */
    public function addStarPrefix()
    {
        /*
        Adds a /* prefix if there is no prefix.  This is when you need
        to keep context's constrained to a single parent.
        */
        if ($this->path) {
            $this->path .= '*/';
        } else {
            $this->path = '*/';
        }

        $this->starPrefix = true;
    }

    /**
     * Joins this XPath expression with $other (another XPath expression) using
     * $combiner to join them.
     *
     * @param string    $combiner The combiner string.
     * @param XPathExpr $other    The other XPath expression to combine with
     *                            this one.
     */
    public function join($combiner, $other)
    {
        $prefix = (string) $this;

        $prefix .= $combiner;
        $path = $other->getPrefix().$other->getPath();

        /* We don't need a star prefix if we are joining to this other
             prefix; so we'll get rid of it */
        if ($other->hasStarPrefix() && '*/' == $path) {
            $path = '';
        }
        $this->prefix = $prefix;
        $this->path = $path;
        $this->element = $other->getElement();
        $this->condition = $other->GetCondition();
    }

    /**
     * Gets an XPath literal for $s.
     *
     * @param mixed $s Can either be a ElementNode or a string.
     *
     * @return string
     */
    public static function xpathLiteral($s)
    {
        if ($s instanceof ElementNode) {
            // This is probably a symbol that looks like an expression...
            $s = $s->formatElement();
        } else {
            $s = (string) $s;
        }

        if (false === strpos($s, "'")) {
            return sprintf("'%s'", $s);
        }

        if (false === strpos($s, '"')) {
            return sprintf('"%s"', $s);
        }

        $string = $s;
        $parts = array();
        while (true) {
            if (false !== $pos = strpos($string, "'")) {
                $parts[] = sprintf("'%s'", substr($string, 0, $pos));
                $parts[] = "\"'\"";
                $string = substr($string, $pos + 1);
            } else {
                $parts[] = "'$string'";
                break;
            }
        }

        return sprintf('concat(%s)', implode($parts, ', '));
    }
}



/**
 * XPathExprOr represents XPath |'d expressions.
 *
 * Note that unfortunately it isn't the union, it's the sum, so duplicate elements will appear.
 *
 * This component is a port of the Python lxml library,
 * which is copyright Infrae and distributed under the BSD license.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MyMailXPathExprOr extends MyMailXPathExpr
{
    /**
     * Constructor.
     *
     * @param array  $items  The items in the expression.
     * @param string $prefix Optional prefix for the expression.
     */
    public function __construct($items, $prefix = null)
    {
        $this->items = $items;
        $this->prefix = $prefix;
    }

    /**
     * Gets a string representation of this |'d expression.
     *
     * @return string
     */
    public function __toString()
    {
        $prefix = $this->getPrefix();

        $tmp = array();
        foreach ($this->items as $i) {
            $tmp[] = sprintf('%s%s', $prefix, $i);
        }

        return implode($tmp, ' | ');
    }
}