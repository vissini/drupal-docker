(function (Drupal) {
  Drupal.behaviors.farosLogin = {
    attach: function (context, settings) {
      window.togglePasswordRequirements = function () {
        var requirementsElement = document.getElementById('password-requirements');
        if (requirementsElement) {
          var requirementsContent = requirementsElement.innerHTML;
          var dialogHtml = '<div id="password-requirements-modal">' +
            requirementsContent + 
            '<button type="button" class="close-password-requirements">' + Drupal.t('Ok, I understand') + '</button>' +
            '</div>';
          
          // Remover qualquer modal existente para evitar conflitos
          var existingModal = document.getElementById('password-requirements-modal');
          if (existingModal) {
            existingModal.remove();
          }

          // Adicionar o modal ao body
          var modalContainer = document.createElement('div');
          modalContainer.innerHTML = dialogHtml;
          document.body.appendChild(modalContainer);

          // Inicializar o diálogo
          var modalElement = modalContainer.querySelector('#password-requirements-modal');
          var dialog = Drupal.dialog(modalElement, {
            title: Drupal.t('Password Requirements'),
            width: 'auto',
            buttons: [],
            close: function () {
              modalContainer.remove();
            }
          });

          dialog.showModal();

          // Associar a instância do diálogo ao elemento modal
          modalElement.dialogInstance = dialog;
        } else {
          console.error('Password requirements element is not found.');
        }
      };

      // Delegar o evento de clique para fechar o modal
      document.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('close-password-requirements')) {
          var modal = document.getElementById('password-requirements-modal');
          if (modal && modal.dialogInstance) {
            modal.dialogInstance.close();
            modal.remove();
          }
        }
      });

      // Anexar o evento à span se ainda não estiver anexado.
      var spans = context.querySelectorAll('span.toggle-password-requirements');
      spans.forEach(function (span) {
        if (!span.classList.contains('faros-login-attached')) {
          span.classList.add('faros-login-attached');
          span.addEventListener('click', togglePasswordRequirements);
        }
      });
    }
  };
})(Drupal);
