<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Search_Adapter_Abstract
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to search
 */
abstract class Core_Search_Adapter_Abstract
{
    /**
     * Build string to post by curl
     * @param array $params
     */
    protected function buildQueryString(array $params=array())
    {
        $params = http_build_query($params, null, '&');        
        $params = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $params);
        return preg_replace('/("|\\\)/', '\\\$1', $params);
    }

    /**
     * Escape string when query
     * @param <string> $strQuery
     * @return <string>
     */
    public function escapeQueryString($strQuery)
    {          
        $strQuery = iconv("UTF-8", "UTF-8//IGNORE", $strQuery);        
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';
        $strQuery = preg_replace($pattern, $replace, $strQuery);
        return preg_replace('/[;,:,\\,\[,\],\{,!,^,\}]|OR|AND/', '', $strQuery) ;
    }

    /**
     * Replace control (non-printable) characters from
     * string that are invalid to Solr's XML parser with a space.
     *
     * @param string $string
     * @return string
     */
    protected function stripCtrlChars($string)
    {
        // See:  http://w3.org/International/questions/qa-forms-utf-8.html
        // Printable utf-8 does not include any of these chars below x7F
        return preg_replace('@[\x00-\x08\x0B\x0C\x0E-\x1F]@', ' ', $string);
    }

    /**
     * Get options child
     * @param <array> $options
     * @return <array>
     */
    protected function getOptions($options)
    {
        return $options[$options['adapter']];
    }

    /**
     * Ping server
     */
    abstract protected function ping();

    /**
     * Search query string
     * @param array $options
    */
    abstract protected function search(array $options=array());
    
    /**
     * Suggestion query string
     * @param array $options
    */
    abstract protected function suggest(array $options=array());

    /**
     * Add index to search engine
     * @param array $arrDocuments
     */
    abstract protected function index(array $arrDocuments);

    /**
     * Update index to search engine
     * @param array $arrDocuments
     */
    abstract protected function update(array $arrDocuments);
    
    /**
     * Add index to search engine
     * @param array $arrDocuments
     */
    abstract protected function add(array $arrDocuments);
    
    /**
     * Increment index to search engine
     * @param array $arrDocuments
     */
    abstract protected function increment(array $arrDocuments);

    /**
     * Delete index by id
     * @param var $id
     */
    abstract protected function deleteById($id);

    /**
     * Delete index by array id
     * @param array $arrIds
     */
    abstract protected function deleteByIds(array $arrIds=array());

    /**
     * Delete index by query string
     * @param string $query
     */
    abstract protected function deleteByQuery(string $query);

    /**
     * Commit search engine
     * @param array $options
     */
    abstract protected function commit(array $options=array());

    /**
     * Optimize search engine
     * @param array $options
     */
    abstract protected function optimize(array $options=array());

    /**
     * The rollback command rollbacks all add/deletes made to the index since the last commit.
     * It neither calls any event listeners nor creates a new searcher.
     */
    abstract protected function rollback();

    /**
     * rebuild all index
     */
    abstract protected function flush();
}

