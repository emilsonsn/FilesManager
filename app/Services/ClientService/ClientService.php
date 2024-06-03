<?php

namespace App\Services\ClientService;

use App\Models\Client;
use App\Models\ClientStage;
use App\Models\Credentials;
use App\Models\KanbanCard;


class ClientService {

    public function getClients($request){
        $search = $request->search ?? '';
        $date = $request->date ?? '';
        $order = $request->order ?? 'desc';
        $airport = $request->airport ?? '';
        $clients = Client::orderBy('id', $order);

        if($search){
            $clients->where('name', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('phone', 'like', "%$search%");
        }

        if($date){
            $clients->whereDate('accession_date', $date);
        }

        if ($airport) {
          $airports = explode(' - ', $airport);
          $clients->where(function ($query) use ($airports) {
              foreach ($airports as $airport) {
                  $query->orWhere('nearby_airports', 'LIKE', '%' . $airport . '%');
              }
          });
      }


        return [$clients->get(), $search, $date, $order, $airport];
    }

    public function create($request){
        $request = $request->all();
        $client = Client::create($request);

        $firstClientStage = ClientStage::orderBy('id', 'asc')->first();
        KanbanCard::create([
            'client_id' => $client->id,
            'client_stage_id' => $firstClientStage->id
        ]);
    }

    public function update($request){
        $request = $request->all();
        $client = Client::find($request['id'])->update($request);
    }

    public function delete($id){
        $client = Client::find($id);
        $client->delete();
    }

    public function client_stage($request){
        $lastClientStage = ClientStage::orderBy('id', 'desc')->first();
        $stage = ($lastClientStage->stage ?? 0) + 1;
        ClientStage::create([
            'title' => $request->title,
            'color' => $request->color,
            'stage' => $stage
        ]);
    }

    public function update_stage($id, $direction){
        $clientStage = ClientStage::find($id);

        $stage = $clientStage->stage;

        if($direction == 'left'){
            $stageBefore = ClientStage::orderBy('stage', 'desc')->where('stage', '<', $clientStage->stage)->first();
            if($stageBefore){
                $stage = $stageBefore->stage + 1;
                $stageBefore->update(['stage' => $clientStage->stage]);
                $stage-=1;
            }
        }

        if($direction == 'right'){
            $stageAfter = ClientStage::orderBy('stage', 'asc')->where('stage', '>', $clientStage->stage)->first();
            if($stageAfter){
                $stage = $stageAfter->stage - 1;
                $stageAfter->update(['stage' => $clientStage->stage]);
                $stage+=1;
            }
        }

        $clientStage->update(['stage' => $stage]);
    }

    public function update_card($cardId, $columnId){
        $kanbanCard = KanbanCard::find($cardId);
        $kanbanCard->update([
            'client_stage_id' => $columnId
        ]);
        return $kanbanCard;
    }

    public function delete_client_stage($id){
        $clientStage = ClientStage::find($id);
        $clientStage->delete();
    }

    public function credentials_create($request){
        $request = $request->all();
        if(isset($request['id']) && $request['id']){
            $credentials = Credentials::find($request['id']);
            $credentials->update($request);
        }else{
            Credentials::create($request);
        }
    }

    public function credentials_setStatus($id, $status){
        $credentials = Credentials::find($id);
        if($status >= 0 and $status < 3){
            $credentials->update([
                'status' => $status
            ]);
        }
    }

    public function credentials_delete($id){
        Credentials::find($id)->delete();
    }

}
