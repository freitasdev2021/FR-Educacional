<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
       <div class="fr-card-header">
          @foreach($submodulos as $s)
          <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
          @endforeach
       </div>
       <div class="fr-card-body">
          <!--CABECALHO-->
          <form class="col-sm-12 p-2 row" action="{{route("Escolas/Anosletivos/Save")}}" method="POST">
            @csrf
            @if(session('success'))
             <div class="col-sm-12 shadow p-2 bg-success text-white">
                <strong>{{session('success')}}</strong>
             </div>
             @elseif(session('error'))
             <div class="col-sm-12 shadow p-2 bg-danger text-white">
                <strong>{{session('error')}}</strong>
             </div>
             <br>
             @endif
             <div class="col-auto">
               <label>Filtre pela Escola</label>
               <select class="form-control">
                  <option>Selecione</option>
                  @foreach($Escolas as $es)
                  <option value="{{$es->id}}">{{$es->Nome}}</option>
                  @endforeach
               </select>
             </div>
             @if(isset($AnoLetivo[0]->IDAno))
             <input type="hidden" name="id" value="{{$AnoLetivo[0]->IDAno}}">
             @endif
             <div class="col-sm-2">
               <label>O Ano vai De</label>
               <input type="date" name="INIAno" class="form-control" value="{{isset($AnoLetivo[0]->INIAno) ? $AnoLetivo[0]->INIAno : ''}}">
            </div>
            <div class="col-sm-2">
               <label>Até</label>
               <input type="date" name="TERAno" class="form-control" value="{{isset($AnoLetivo[0]->TERAno) ?$AnoLetivo[0]->TERAno : ''}}">
            </div>
            <div class="col-auto">
               <label style="visibility: hidden">a</label>
               <input type="submit" class="form-control btn btn-success" value="Salvar Ano Letivo">
            </div>
          </form>
          <!--LISTAS-->
          <link rel="stylesheet" href="{{asset('plugins/calendar/css/style.css')}}">
          <div class="col-sm-12 p-2">
             <hr>
             <div class="d-flex justify-content-center">
                <div class="row shadow">
                   <div class="col-md-12">
                      <div class="calendar-section">
                         <div class="row no-gutters">
                            <div class="col-md-6">
                               <div class="calendar calendar-first" id="calendar_first">
                                  <div class="calendar_header">
                                     <button class="switch-month switch-left">
                                     <i class='bx bx-left-arrow-alt' ></i>
                                     </button>
                                     <h2></h2>
                                     <button class="switch-month switch-right">
                                        <i class='bx bx-right-arrow-alt' ></i>
                                     </button>
                                  </div>
                                  <div class="calendar_weekdays"></div>
                                  <div class="calendar_content"></div>
                               </div>
                            </div>
                            <div class="col-md-6">
                               <div class="calendar calendar-second" id="calendar_second">
                                  <div class="calendar_header">
                                     <button class="switch-month switch-left">
                                        <i class='bx bx-left-arrow-alt' ></i>
                                     </button>
                                     <h2></h2>
                                     <button class="switch-month switch-right">
                                        <i class='bx bx-right-arrow-alt' ></i>
                                     </button>
                                  </div>
                                  <div class="calendar_weekdays"></div>
                                  <div class="calendar_content"></div>
                               </div>
                            </div>
                         </div>
                         <!-- End Row -->
                      </div>
                      <!-- End Calendar -->
                   </div>
                </div>
             </div>
          </div>
          <script src="{{asset('plugins/calendar/js/popper.js')}}"></script>
          <script src="{{asset('plugins/calendar/js/main.js')}}"></script>
          <script src="{{asset('plugins/calendar/js/calendario.js')}}"></script>
          <!--//-->
       </div>
    </div>
 </x-educacional-layout>