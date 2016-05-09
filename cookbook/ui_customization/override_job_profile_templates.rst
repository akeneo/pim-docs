How to override job profiles templates
======================================

It is possible to override job profile templates used to display information about the jobs (import and export jobs).

It is possible to override per job templates.

First, we need to create a twig template to override the default one. You can use the

The template for showing the job profile:
.. code-block:: html
        # /src/Acme/Bundle/AppBundle/Resources/views/custom_show_template.html.twig

        {% extends 'PimImportExportBundle:JobProfile:show.html.twig' %}

        {% oro_title_set({ params: {"%job.label%": jobInstance.label } }) %}

        {% set entityName        = 'import profile' %}
        {% set action            = 'import' %}
        {% set indexRoute        = path('pim_importexport_import_profile_index') %}
        {% set editRoute         = path('pim_importexport_import_profile_edit', {'id': jobInstance.id}) %}
        {% set launchRoute       = path('pim_importexport_import_profile_launch', {'id': jobInstance.id}) %}
        {% set launchUploadRoute = path('pim_importexport_import_profile_launch_upload', {'id': jobInstance.id}) %}


Template for editing the job profile:
.. code-block:: html
        # /src/Acme/Bundle/AppBundle/Resources/views/custom_edit_template.html.twig
        {% oro_title_set({ params: {"%job.label%": form.vars.value.label } }) %}

        {% set actionRoute = path('pim_importexport_import_profile_edit', { 'id': form.vars.value.id }) %}

        {% set entityName = 'import profile' %}
        {% set title = (entityName ~ '.edit')|trans ~ ' - ' ~ form.vars.value.label %}

        {% set indexRoute = path('pim_importexport_import_profile_index') %}
        {% if form.vars.value.id %}
        {% set removeAcl = 'pim_importexport_import_profile_remove' %}
        {% set removeRoute = path('pim_importexport_import_profile_remove', { 'id': form.vars.value.id }) %}
        {% set importRoute = path('pim_importexport_import_profile_index') %}
        {% set removeMessage = 'confirmation.remove.import profile'|trans({ '%name%': form.vars.value.label }) %}
        {% set removeSuccessMessage = 'flash.import.removed'|trans %}
        {% endif %}

        {% include 'PimImportExportBundle:JobProfile:edit.html.twig' %}

In order to override default template you will need to register your custom template by creating new configuration file in ``AcmeBundle\Resources``.

.. code-block:: yaml
        :linenos:
        # /src/Acme/Bundle/AppBundle/Resources/config/job_templates.yml

        job_templates:
            custom_job_name:
                templates:
                    show: pimacme:custom_show_template.html.twig
                    edit: pimacme:custom_edit_template.html.twig

Now you can visit your 'custom_job_name' profile and see your changes applied.
