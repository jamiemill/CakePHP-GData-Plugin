<?php
/**
 * Plugin model for "Google Analytic".
 *
 * Provides custom find types for the various calls on the web service, mapping
 * familiar CakePHP methods and parameters to the http request params for
 * issuing to the web service.
 *
 * @author Neil Crookes <neil@neilcrookes.com>
 * @link http://www.neilcrookes.com
 * @copyright (c) 2010 Neil Crookes
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 */
class GoogleAnalytic extends GdataAppModel {

  /**
   * The datasource this model uses
   *
   * @var name
   */
  public $useDbConfig = 'googleAnalytics';

  /**
   * The custom find types available on this model
   * 
   * @var array
   */
  public $_findMethods = array(
    'data' => true,
    'accounts' => true,
  );

  /**
   * Defines the prefix to be added to metrics, dimensions etc
   * @var string
   */
  public $prefix = 'ga:';

  /**
   * Dimensions and their descriptions, from the google anaylytics documentation
   * @var array
   */
  public $dimensions = array(
   'Visitor' => array(
      'browser' => 'The names of browsers used by visitors to your website. For example, "Internet Explorer" or "Firefox." The version of the browser is not returned in this field.',
      'browserVersion' => 'The browser versions used by visitors to your site. For example, 2.0.0.14',
      'city' => 'The cities of site visitors, derived from IP addresses. The city field falls in a hierarchy of geographical groupings used in Analytics, which proceeds in the following order: continent, sub-continent, country, region, sub-region, and city.',
      'connectionSpeed' => 'The qualitative network connection speeds of site visitors. For example, T1, DSL, Cable, Dialup.',
      'continent' => 'The continents of site visitors, derived from IP addresses.',
      'countOfVisits' => 'Number of visits to your site. This is calculated by determining the number of visitor sessions. For example, if a visitor comes to your site, exits their browser, and 5 minutes later visits your site again via the same browser, that is calculated as 2 visits.',
      'country' => 'The countries of site visitors, derived from IP addresses.',
      'date' => 'The date of the visit. An integer in the form YYYYMMDD.',
      'day' => 'The day of the month from 01 to 31.',
      'daysSinceLastVisit' => 'The number of days elapsed since visitors last visited the site. Used to calculate visitor loyalty. For example, if you view this field in a report on 5/20, and some visitors last visited your site on 5/15, the value for this would be 5, reported as "5 days ago."',
      'flashVersion' => 'The versions of Flash supported by visitors\' browsers, including minor versions.',
      'hostname' => 'The hostnames visitors used to reach your site. In other words, if some visitors use www.googlestore.com to reach your site, this string appears as one of the hostnames used to reach your site. However, if other visitors also come to your site via googlestore.com or via an IP redirect from a search engine result (66.102.9.104), those values will also be present in this field.',
      'hour' => 'A two digit hour of the day ranging from 00-23. (Google Analytics does not track visitor time more precisely than hours.) Note: Combining this dimension with ga:adContent is not currently supported.',
      'javaEnabled' => 'Whether the visitor has Java support enabled on their browser. The possible values are Yes or No.',
      'language' => 'This field uses the language as provided by the HTTP Request for the browser to determine the primary languages used by visitors. Values are given as an ISO-639 code (e.g. en-gb for British English).',
      'latitude' => 'The approximate latitude of the visitor\'s city. Locations north of the equator are represented by positive values and locations south of the equator by negative values.',
      'longitude' => 'The approximate longitude of the visitor\'s city. Locations east of the meridian are represented by positive values and locations west of the meridian by negative values.',
      'month' => 'The month of the visit. A two digit integer from 01 to 12.',
      'networkDomain' => 'The domain name of the ISPs used by visitors to your website.',
      'networkLocation' => 'The name of service providers used to reach your site. For example, if most visitors to your site come via the major service providers for cable internet, you will see the names of those cable service providers in this element.',
      'pageDepth' => 'The number of pages visited by visitors during a session (visit). The value is a histogram that counts pageviews across a range of possible values. In this calculation, all visits will have at least one pageview, and some percentage of visits will have more.',
      'operatingSystem' => 'The operating system used by your visitors. For example, Windows, Linux, Macintosh, iPhone, iPod.',
      'operatingSystemVersion' => 'The version of the operating system used by your visitors, such as XP for Windows or PPC for Macintosh.',
      'region' => 'The region of site visitors, derived from IP addresses. In the U.S., a region is a state, such as New York.',
      'screenColors' => 'The color depth of visitors\' monitors, as retrieved from the DOM of the visitor\'s browser. Values include 4-bit, 8-bit, 24-bit, or undefined-bit.',
      'screenResolution' => 'The screen resolution of visitors\' monitors, as retrieved from the DOM of the visitor\'s browser. For example: 1024x738.',
      'subContinent' => 'The sub-continent of site visitors, derived from IP addresses. For example, Polynesia or Northern Europe.',
      'userDefinedValue' => 'The value provided when you define custom visitor segments for your site. For more information, see Creating Custom Visitor Segments.',
      'visitorType' => 'A boolean indicating if visitors are new or returning. Possible values: New Visitor, Returning Visitor.',
      'week' => 'The week of the visit. A two-digit number from 01 to 52.',
      'year' => 'The year of the visit. A four-digit year from 2005 to the current year.',
    ),
   'Campaign' => array(
      'adContent' => 'The first line of the text for your online Ad campaign. If you are using mad libs for your AdWords content, this field displays the keywords you provided for the mad libs keyword match. Note: Combining this dimension with ga:hour is not currently supported.',
      'adGroup' => 'The ad groups that you have identified for your campaign keywords. For example, you might have an ad group toys which you associate with the keywords fuzzy bear.',
      'adSlot' => 'The position of the advertisement as it appears on the host page. For example, the online advertising position might be side or top.',
      'adSlotPosition' => 'The order of the online advertisement as it appears along with other ads in the position on the page. For example, the ad might appear on the right side of the page and be the 3rd ad from the top.',
      'campaign' => 'The name(s) of the online ad campaign that you use for your website.',
      'keyword' => 'The keywords used by visitors to reach your site, via both paid ads and through search engine results.',
      'medium' => 'The type of referral to your website. For example, when referring sources to your website are search engines, there are a number of possible mediums that can be used from a search engine referral: from a search result (organic) and from an online ad on the search results page (CPC, ppc, cpa, CPM, cpv, cpp).',
      'referralPath' => 'The path of the referring URL. If someone places a link to your site on their website, this element contains the path of the page that contains the referring link.',
      'source' => 'The domain (e.g. google.com) of the source referring the visitor to your website. The value for this dimension sometimes contains a port address as well.',
    ),
   'Content' => array(
      'exitPagePath' => 'The last page of the session (or "exit" page) for your visitors.',
      'landingPagePath' => 'The path component of the URL of the entrance or "landing" page for your visitors.',
      'pagePath' => 'The page on your website by path and/or query parameters.',
      'pageTitle' => 'The title for the page, as specified in the <title></title> element of the HTML document.',
    ),
   'Ecommerce' => array(
      'affiliation' => 'Typically used to designate a supplying company or brick and mortar location; product affiliation.',
      'daysToTransaction' => 'The number of days between users\' purchases and the related campaigns that lead to the purchases.',
      'productCategory' => 'Any product variations (size, color) for purchased items as supplied by your ecommerce application.',
      'productName' => 'The product name for purchased items as supplied by your ecommerce tracking method.',
      'productSku' => 'The product codes for purchased items as you have defined them in your ecommerce tracking application.',
      'transactionId' => 'The transaction ID for the shopping cart purchase as supplied by your ecommerce tracking method.',
    ),
   'Internal Search' => array(
      'searchCategory' => 'If you have categories enabled for internal site search, this field identifies the categories used for the internal search. For example, you might have product categories for internal search, such as electronics, furniture, or clothing.',
      'searchDestinationPage' => 'The page that the user visited after performing an internal site search.',
      'searchKeyword' => 'Search terms used by site visitors on your internal site search.',
      'searchKeywordRefinement' => 'Subsequent keyword search terms or strings entered by users after a given initial string search.',
      'searchStartPage' => 'The page where the user initiated an internal site search.',
      'searchUsed' => 'A boolean which separates visitor activity depending upon whether internal search activity occured or did not occur. Values are Visits With Site Search and Visits Without Site Search.',
    ),
  );

  /**
   * Metrics and their descriptions, from the google anaylytics documentation
   * @var array
   */
  var $metrics = array(
   'Visitor' => array(
      'bounces' => 'The total number of single-page visits to your site.',
      'entrances' => 'The number of entrances to your site. The value will always be equal to the number of visits when aggregated over your entire website. Thus, this metric is most useful when combined with dimensions such as ga:landingPagePath, at which point entrances as a metric indicates the number of times a particular page served as an entrance to your site.',
      'exits' => 'The number of exits from your site. As with entrances, it will always be equal to the number of visits when aggregated over your entire website. Use this metric in combination with content dimensions such as ga:exitPagePath in order to determine the number of times a particular page was the last one viewed by visitors.',
      'newVisits' => 'The number of visitors whose visit to your site was marked as a first-time visit.',
      'pageviews' => 'The total number of pageviews for your site when aggregated over the selected dimension. For example, if you select this metric together with ga:pagePath, it will return the number of page views for each URI.',
      'timeOnPage' => 'How long a visitor spent on a particular page or set of pages. Calculated by subtracting the initial view time for a particular page from the initial view time for a subsequent page. Thus, this metric does not apply to exit pages for your site.',
      'timeOnSite' => 'The total duration of visitor sessions over the selected dimension. For example, suppose you combine this field with a particular ad campaign. In this case, the metric will display the total duration of all visitor sessions for those visitors who came to your site via a particular ad campaign. You could then compare this metric to the duration of all visitors who came to your site through means other than the particular ad campaign. This would then give you a side-by-side comparison and a means to calculate the boost in visit duration provided by a particular campaign.',
      'visitors' => 'Total number of visitors to your site for the requested time period. When requesting this metric, you can only combine it with time dimensions such as ga:hour or ga:year.',
      'visits' => 'The total number of visits over the selected dimension. A visit consists of a single-user session, which times out automatically after 30 minutes unless the visitor continues activity on your site, or unless you have adjusted the user session in the ga.js tracking for your site. See Adjusting the User Session for more information.',
    ),
   'Campaign' => array(
      'adClicks' => 'The total number of times users have clicked on an ad to reach your site.',
      'adCost' => 'Derived cost for the advertising campaign. The currency for this value is based on the currency that you set in your AdWords account.',
      'CPC' => 'Cost to advertiser per click.',
      'CPM' => 'Cost per thousand impressions.',
      'CTR' => 'Click-through-rate for your ad. This is equal to the number of clicks divided by the number of impressions for your ad (e.g. how many times users clicked on one of your ads where that ad appeared).',
      'impressions' => 'Total number of campaign impressions.',
    ),
   'Content' => array(
      'uniquePageviews' => 'The number of different (unique) pages within a visit, summed up across all visits',
    ),
   'Ecommerce' => array(
      'itemRevenue' => 'Total revenue from purchased product items on your site. See the tracking API reference for _addItem() for additional information.',
      'itemQuantity' => 'The total number of items purchased. For example, if users purchase 2 frisbees and 5 tennis balls, 7 items have been purchased.',
      'transactionRevenue' => 'The total sale revenue, including shipping and tax, if provided in the transation. See the documentation for _addTrans() in the tracking API reference for additional information.',
      'transactions' => 'The total number of transactions.',
      'transactionShipping' => 'The total cost of shipping.',
      'transactionTax' => 'The total amount of tax.',
      'uniquePurchases' => 'The number of product sets purchased. For example, if users purchase 2 frisbees and 5 tennis balls from your site, 2 product sets have been purchased.',
    ),
   'Internal Search' => array(
      'searchDepth' => 'The average number of subsequent page views made on your site after a use of your internal search feature.',
      'searchDuration' => 'The visit duration to your site where a use of your internal search feature occurred.',
      'searchExits' => 'The number of exits on your site that occurred following a search result from your internal search feature.',
      'searchRefinements' => 'The number of refinements made on an internal search.',
      'searchUniques' => 'The number of unique visitors to your site who used your internal search feature.',
      'searchVisits' => 'The total number of visits to your site where a use of your internal search feature occurred.',
    ),
   'Goals' => array(
      'goal1Completions' => 'The total number of completions for goal 1.',
      'goal2Completions' => 'The total number of completions for goal 2.',
      'goal3Completions' => 'The total number of completions for goal 3.',
      'goal4Completions' => 'The total number of completions for goal 4.',
      'goalCompletionsAll' => 'The total number of completions for all goals defined for your profile.',
      'goal1Starts' => 'The total number of starts for goal 1.',
      'goal2Starts' => 'The total number of starts for goal 2.',
      'goal3Starts' => 'The total number of starts for goal 3.',
      'goal4Starts' => 'The total number of starts for goal 4.',
      'goalStartsAll' => 'The total number of starts for all goals defined for your profile.',
      'goal1Value' => 'The total numeric value for goal 1.',
      'goal2Value' => 'The total numeric value for goal 2.',
      'goal3Value' => 'The total numeric value for goal 3.',
      'goal4Value' => 'The total numeric value for goal 4.',
      'goalValueAll' => 'The total value for all goals defined for your profile.',
    ),
  );

  /**
   * Custom find type to fetch Google Analyic data
   *
   * GoogleAnalytic::find('data'[, $options]);
   *
   * **Options:**
   *
   *  - conditions
   *   - ids - comma separated string of integers corresponding to the table ids
   *   - start-date - yyyy-mm-dd
   *   - end-date - yyyy-mm-dd
   *  - fields - comma separated strings, choose from metrics (see above)
   *  - group - comma separated strings, choose from dimensions (see above)
   *  - limit - integer
   *  - page - integer
   *  - order - string metric/dimension (asc|desc)
   *
   * @param string $state
   * @param array $query
   * @param array $results
   * @return array
   */
  protected function _findData($state, $query = array(), $results = array()) {

    if ($state == 'before') {

      if (isset($query['conditions']['ids'])) {
        $this->request['uri']['query']['ids'] = $this->_prefix($query['conditions']['ids']);
      } else {
        trigger_error(__('You must specify the ids', true), E_USER_ERROR);
      }

      if (isset($query['conditions']['start-date'])) {
        $this->request['uri']['query']['start-date'] = $query['conditions']['start-date'];
      } else {
        trigger_error(__('You must specify a start date', true), E_USER_ERROR);
      }

      if (isset($query['conditions']['end-date'])) {
        $this->request['uri']['query']['end-date'] = $query['conditions']['end-date'];
      } else {
        trigger_error(__('You must specify an end date', true), E_USER_ERROR);
      }

      if (isset($query['fields'])) {
        $this->request['uri']['query']['metrics'] = $this->_prefix($query['fields']);
      } else {
        trigger_error(__('You must specify some metrics in the fields key', true), E_USER_ERROR);
      }

      if (isset($query['group'])) {
        $this->request['uri']['query']['dimensions'] = $this->_prefix($query['group']);
      } else {
        trigger_error(__('You must specify some dimensions in the group key', true), E_USER_ERROR);
      }

      if (isset($query['limit'])) {
        $this->request['uri']['query']['max-results'] = $query['limit'];
      } else {
        $this->request['uri']['query']['max-results'] = $query['limit'] = 50;
      }

      if (isset($query['page'])) {
        $this->request['uri']['query']['start-index'] = 1 + ($query['page'] - 1) * $query['limit'];
      } else {
        $this->request['uri']['query']['start-index'] = $query['page'] = 1;
      }

      // Order is represented by sort string with minus sign, if desc, followed
      // by the metric or dimension being sorted, prefixed by 'prefix'
      if (isset($query['order'])) {
        if (is_string($query['order'])) {
          $query['order'] = explode(',', $query['order']);
        }
        $sorts = array();
        foreach ($query['order'] as $order) {
          list($sort, $direction) = preg_split('/\s/', $order);
          if (substr($sort, 0, 3) != $this->prefix) {
            $sort = $this->prefix .= $sort;
          }
          if (strtolower($direction) == 'desc') {
            $sort = '-' . $sort;
          }
          $sorts[] = $sort;
        }
        $this->request['uri']['query']['sort'] = implode(',', $sorts);
      }

      if (Configure::read('debug') > 0) {
        $this->request['uri']['query']['prettyprint'] = 1;
      }

      $this->request['uri']['path'] = 'data';

      $this->request['auth'] = true;

      return $query;

    } else {

      return $results;

    }

  }

  /**
   * Prefixes an array of strings, or a comma separated string with 'prefix'
   * 
   * @param mixed $items
   * @return string
   */
  protected function _prefix($items) {
    if (is_string($items)) {
      $items = explode(',', $items);
    }
    foreach ($items as $k => $item) {
      if (substr($item, 0, 3) != $this->prefix) {
        $items[$k] = $this->prefix . $item;
      }
    }
    return implode(',', $items);
  }

  /**
   * Custom find type that lists the profiles this user has access to.
   * 
   * @param array $state
   * @param array $query
   * @param array $results
   * @return mixed
   */
  protected function _findAccounts($state, $query = array(), $results = array()) {
    if ($state == 'before') {
      $this->request['uri']['path'] = 'accounts/default';
      if (Configure::read('debug') > 0) {
        $this->request['uri']['query']['prettyprint'] = 1;
      }
      $this->request['auth'] = true;
      return $query;
    } else {
      return $results;
    }
  }

  /**
   * CakePHP's Controller::paginate() method checks whether a field exists in
   * the model being paginated before detecting sort params in the url and thus
   * the Paginator helper being able to toggle sort directions
   * 
   * @return boolean
   */
  public function hasField() {
    return true;
  }
  
}

?>