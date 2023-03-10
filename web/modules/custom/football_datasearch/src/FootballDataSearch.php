<?php
  /*
    Implements a search from that retrieves data from an external API.
  */

  use Drupal\Core\Form\FormBase;
  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\Http\ClientFactory;
  use GuzzleHttp\Client;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  /*Defines a form that allows users to search data from an external API.*/
  class FootballDataSearch extends FormBase{
    protected $httpClient;

    //Constructs a ClientFactory object.
    public function __construct(ClientFactory $http_client){
        $this->httpClient = $http_client;
    }

    /*Create a ContainerInterface object.*/
    public static function create(ContainerInterface $container){
      return new static(
        $container->get('http_client_factory')
      );
    }

    public function getFormId(){
      return 'football_data_search';
    }

    /*Create a FormStateInterface object.*/
    public function buildForm(array $form, FormStateInterface $form_state){
        //Build a textfield search bar
        $form['search'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Search:')
        ];

        //Build a submit button
      $form['submit'] = [
          '#type' => 'submit',
          '#title' => $this->t('Submit')
      ];

        //Build the container that displays the results
      $form['results'] = [
        '#type'=> 'container',
        '#attributes' => [
          'id' => 'search-results'
          ]
        ];

        //Render the form
      return $form;
    }

    //Handle form submission
    //&$form value is inherited from $form in the buildForm() function
    public function submitForm(array &$form, FormStateInterface $form_state){
      //Create a Guzzle client object
      $client = new Client();
      $search_query = $form_state->getValue('search');

      //Send a response
      $response = $client->request('GET', 'https://nfl-team-stats1.p.rapidapi.com/teamStats/search', [
        'query' =>[
            'q' => $search_query,
            'key' => '407db46b8emsh3c9961abad4d562p1a6ff4jsn6e0efb102d31'
        ]
      ]);

      //Convert the JSON response object to a String
      $data = json_decode($response->getBody());

      //Display the results to the user.
      $results = [];
      foreach($data as $item){
        //Render each item as a markup
        $results[] = [
          '#markup' => $item->title
        ];
      }

      $form['results'] = [
        '#type' => 'container',
        '#attributes' => [
          'id' => 'mymodule-search-results'
        ],
        '#children' => $results,
      ];
    }
  }
