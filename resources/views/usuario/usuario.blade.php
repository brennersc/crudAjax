@extends('layouts.app')

@section('content')
    <div class="card border border-dark">
        <div class="card-header">
            <div class="row">
                <div class="col-md-7 col-sm-12">
                    <h3>Usuários</h3>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <div id="retornar" class="procurar"></div>
            <table class="table table-ordered table-hover" id="tabelaUsuario">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-primary" onclick="novoUsuario()" role="button">Novo Usuário</a>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id="dlgUsuario">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="formUsuario" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Usuário</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" class="form-control">
                        <div class="form-group">
                            <label for="nome" class="control-label">Nome *</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="nome" placeholder="Nome do Usuário">
                            </div>
                            <span class='text-danger' id='nameError'></span>
                        </div>
                        <div class="form-group">
                            <label for="email" class="control-label">Email *</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="email" placeholder="Email">
                            </div>
                            <span class='text-danger' id='emailError'></span>
                        </div>
                        <div class="form-group">
                            <label for="senha" class="control-label">Senha *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="senha">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <button type="cancel" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="remover" tabindex="-1" role="dialog" aria-labelledby="TituloModalCentralizado"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalCentralizado">Excluir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id='excluir'>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        //ABRIR MODAL
        var novoUsuario = function() {
            $('#id').val('');
            $('#nome').val('');
            $('#email').val('');
            $('#senha').val('');
            $('#nameError').addClass('d-none');
            $('#emailError').addClass('d-none');
            $('#dlgUsuario').modal('show');
        };

        //LISTA USUARIOS
        function carregarusuario() {
            $.getJSON('/api/usuario', function(usuario) {
                for (i = 0; i < usuario.length; i++) {
                    linha = montarLinha(usuario[i]);
                    $('#tabelaUsuario>tbody').append(linha);
                }
            });
        }

        //Montar Lista Usuarios
        function montarLinha(usuario) {
            var linha =
                "<tr>" +
                "<td>" + usuario.id + "</td>" +
                "<td>" + usuario.name + "</td>" +
                "<td>" + usuario.email + "</td>" +
                "<td>" +
                '<div class="btn-group" role="group" aria-label="Basic example">' +
                '<button class="btn btn-sm btn-primary" onClick="editar(' + usuario.id +
                ')" @guest disabled @endguest><i class="far fa-edit"></i> Editar </button> ' +
                '<button class="btn btn-sm btn-danger" onClick="modalremover(' + usuario.id +
                ')" @guest disabled @endguest><i class="far fa-trash-alt"></i> Apagar </button> ' +
                '</div>' +
                "</td>" +
                "</tr>";
            return linha;
        }

        //CHAMAR MODAL DADOS USUARIO SELECIONADO
        var editar = function(id) {
            $.getJSON('/api/usuario/' + id, function(data) {
                $('#id').val(data.id);
                $('#nome').val(data.name);
                $('#email').val(data.email);
                $('#nameError').addClass('d-none');
                $('#emailError').addClass('d-none');
                $('#dlgUsuario').modal('show');
            });
        }

        //CHAMAR MODAL DADOS REMOVER USUARIO SELECIONADO
        var modalremover = function(id) {
            $('#linha').remove();
            $exlinha = 0;
            $.getJSON('/api/usuario/' + id, function(data) {
                id = data.id;
                nome = data.name;
                exlinha =
                    '<div class="row" id="linha">' +
                    '<div class="col-9">' +
                    '<strong> Tem certeza que deseja excluir o usuário: ' + nome + '?</strong>' +
                    '</div>' +
                    '<div class="col-3">' +
                    '<button id="apagar" class="btn btn-md btn-danger" onclick="remover(' + id +
                    ')">  Apagar </button>' +
                    '</div>' +
                    '</div>';
                // return exlinha;
                $('#excluir').append(exlinha);
                $('#remover').modal('show');
            });
        }

        //REMOVER USUARIO
        var remover = function(id) {
            $('#linha').remove();
            $('#remover').modal('hide');
            $.ajax({
                type: "DELETE",
                url: "/api/usuario/" + id,
                context: this,
                success: function() {
                    linhas = $("#tabelaUsuario>tbody>tr");
                    e = linhas.filter(function(i, elemento) {
                        return elemento.cells[0].textContent == id;
                    });
                    if (e)
                        e.remove();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        //CRIAR USUARIO
        function criarusuario() {
            usuario = {
                name: $("#nome").val(),
                email: $("#email").val(),
                password: $("#senha").val()
            };
            $.ajax({
                type: "POST",
                url: "/api/usuario",
                context: this,
                data: usuario,
                success: function(data) {
                    usuario = JSON.parse(data);
                    linha = montarLinha(usuario);
                    $('#tabelaUsuario>tbody').append(linha);
                    $("#dlgUsuario").modal('hide');
                },
                error: function(data) {
                    $('#nameError').addClass('d-none');
                    $('#emailError').addClass('d-none');
                    var errors = data.responseJSON;
                    if ($.isEmptyObject(errors) == false) {
                        $.each(errors.errors, function(key, value) {
                            var ErrorID = '#' + key + 'Error';
                            $(ErrorID).removeClass("d-none");
                            $(ErrorID).text(value)
                        })
                    }
                }
            });
        }

        //UPADATE USUARIO
        function salvarusuario() {
            usuario = {
                id: $("#id").val(),
                name: $("#nome").val(),
                email: $("#email").val(),
                password: $("#senha").val()
            };
            $.ajax({
                type: "PUT",
                url: "/api/usuario/" + usuario.id,
                context: this,
                data: usuario,
                success: function(data) {
                    usuario = JSON.parse(data);
                    linhas = $("#tabelaUsuario>tbody>tr");
                    e = linhas.filter(function(i, e) {
                        return (e.cells[0].textContent == usuario.id);
                    });
                    if (e) {
                        e[0].cells[0].textContent = usuario.id;
                        e[0].cells[1].textContent = usuario.name;
                        e[0].cells[2].textContent = usuario.email;
                    }
                    $("#dlgUsuario").modal('hide');
                },
                error: function(data) {
                    $('#nameError').addClass('d-none');
                    $('#emailError').addClass('d-none');
                    var errors = data.responseJSON;
                    if ($.isEmptyObject(errors) == false) {
                        $.each(errors.errors, function(key, value) {
                            var ErrorID = '#' + key + 'Error';
                            $(ErrorID).removeClass("d-none");
                            $(ErrorID).text(value)
                        })
                    }
                }
            });
        }

        //ENVIAR FORMULARIO 
        $("#formUsuario").submit(function(event) {
            event.preventDefault();
            if ($("#id").val() != '') {
                salvarusuario();
            } else {
                criarusuario();
            }
        });

        //CARREGAR LISTA
        $(function() {
            carregarusuario();
        })

    </script>
@endsection
