<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $graphql = [
        [
            'name' => 'List subscription of special application',
            'query'=> 'query{
  Application(id:3){
    id
    uid
    name
    platform{
      id
      name
    }
    created_at
    subscriptions{
      data{
        id
        status
        run{
          id
          name
          run_at
        }
      }
      total
      current_page
      last_page
      has_more_pages
    }
  }
}',
        ],
        [
            'name' => 'List expired application with subscription history',
            'query'=> 'query{
  Applications(status:"expired"){
    data {
      id
      uid
      name
      platform{
        id
        name
      }
      created_at
      subscriptions{
        data{
          id
          status
          run{
            id
            name
            run_at
          }
        }
        total
        current_page
        last_page
        has_more_pages
      }
    }
    total
    current_page
    last_page
    has_more_pages
  }
}',
        ],
        [
            'name' => 'Last Checked with subscription',
            'query'=> 'query{
  lastCheck{
    id
    name
    run_at
    expired_count
    subscriptions{
      data{
        id
        status
        application{
          id
          uid
          name
          platform{
            id
            name
          }
        }
      }
      total
      current_page
      last_page
      has_more_pages
    }
  }
}',
        ],
        [
            'name' => 'Last Checked with Expired subscription',
            'query'=> 'query{
  lastCheck{
    id
    name
    expired_count
    run_at
    subscriptions(status:"expired"){
      data{
        id
        status
        application{
          id
          uid
          name
          platform{
            id
            name
          }
        }
      }
      total
      current_page
      last_page
      has_more_pages
    }
  }
}',
        ]
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $restfuls = [
            [
                'name' => 'List subscription of special application',
                'link' => route('api.apps.subscriptions' , [1,10]),
            ],
            [
                'name' => 'List expired application',
                'link' => route('api.apps' , [1,'status' => 'expired']),
            ],
            [
                'name' => 'subscription of special Checked',
                'link' => route('api.rounds.subscriptions' , 3 ),
            ],
            [
                'name' => 'Last Checked',
                'link' => route('api.rounds.last' ),
            ],
        ];
        return view('home' , [
            'graphQls' => $this->graphql,
            'restfuls' => $restfuls
        ]);
    }
}
