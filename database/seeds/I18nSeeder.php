<?php

use Lkt\Instances\LktTranslation;
use Lkt\Translations\Enums\TranslationType;
use Phinx\Seed\AbstractSeed;

class I18nSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run() : void
    {
        $parent = LktTranslation::createIfMissing('adminHelper', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('duplicatedText', TranslationType::Text, [
            'es' => '(Copia)',
            'en' => '(Copy)',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('translationType', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('text', TranslationType::Text, [
            'es' => 'Texto',
            'en' => 'Text',
        ], $parentId);
        LktTranslation::createIfMissing('textarea', TranslationType::Text, [
            'es' => 'Área de texto',
            'en' => 'Textarea',
        ], $parentId);
        LktTranslation::createIfMissing('many', TranslationType::Text, [
            'es' => 'Diccionario',
            'en' => 'Dictionary',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('accessLevel', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Cualquiera',
            'en' => 'Any user',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Solo usuarios registrados',
            'en' => 'Only logged users',
        ], $parentId);
        LktTranslation::createIfMissing('3', TranslationType::Text, [
            'es' => 'Solo usuarios anónimos',
            'en' => 'Only anonymous users',
        ], $parentId);
        LktTranslation::createIfMissing('4', TranslationType::Text, [
            'es' => 'Solo administradores',
            'en' => 'Only admin users',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('i18nForm', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('property', TranslationType::Text, [
            'es' => 'Propiedad',
            'en' => 'Property',
        ], $parentId);
        LktTranslation::createIfMissing('type', TranslationType::Text, [
            'es' => 'Tipo',
            'en' => 'Type',
        ], $parentId);
        LktTranslation::createIfMissing('value', TranslationType::Text, [
            'es' => 'Valor',
            'en' => 'Value',
        ], $parentId);
        LktTranslation::createIfMissing('children', TranslationType::Text, [
            'es' => 'Traducciones',
            'en' => 'Translations',
        ], $parentId);
        LktTranslation::createIfMissing('addI18n', TranslationType::Text, [
            'es' => 'Agregar traducción',
            'en' => 'Add translation',
        ], $parentId);
        LktTranslation::createIfMissing('addI18nAndNew', TranslationType::Text, [
            'es' => 'Agregar traducción y seguir',
            'en' => 'Add translation and new',
        ], $parentId);
        LktTranslation::createIfMissing('addDictionary', TranslationType::Text, [
            'es' => 'Agregar diccionario',
            'en' => 'Add dictionary',
        ], $parentId);
        LktTranslation::createIfMissing('addDictionaryAndNew', TranslationType::Text, [
            'es' => 'Agregar diccionario y seguir',
            'en' => 'Add dictionary and new',
        ], $parentId);
        LktTranslation::createIfMissing('i18nTitleSingle', TranslationType::Text, [
            'es' => 'Traducción',
            'en' => 'Translation',
        ], $parentId);
        LktTranslation::createIfMissing('i18nTitleMany', TranslationType::Text, [
            'es' => 'Traducciones',
            'en' => 'Translations',
        ], $parentId);
        LktTranslation::createIfMissing('dictionaryTitleSingle', TranslationType::Text, [
            'es' => 'Diccionario',
            'en' => 'Dictionary',
        ], $parentId);
        LktTranslation::createIfMissing('dictionaryTitleMany', TranslationType::Text, [
            'es' => 'Diccionarios',
            'en' => 'Dictionaries',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('userRoleCapability', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('0', TranslationType::Text, [
            'es' => 'Deshabilitado',
            'en' => 'Disabled',
        ], $parentId);
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Elementos propios',
            'en' => 'Owned items',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Todos los elementos',
            'en' => 'All items',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('userThemeModes', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('0', TranslationType::Text, [
            'es' => 'Por defecto del sistema',
            'en' => 'System default',
        ], $parentId);
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Claro',
            'en' => 'Light',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Oscuro',
            'en' => 'Dark',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('userForm', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('firstName', TranslationType::Text, [
            'es' => 'Nombre',
            'en' => 'Name',
        ], $parentId);
        LktTranslation::createIfMissing('lastName', TranslationType::Text, [
            'es' => 'Apellidos',
            'en' => 'Lastname',
        ], $parentId);
        LktTranslation::createIfMissing('email', TranslationType::Text, [
            'es' => 'Email',
            'en' => 'Email',
        ], $parentId);
        LktTranslation::createIfMissing('addUser', TranslationType::Text, [
            'es' => 'Agregar usuario',
            'en' => 'Add user',
        ], $parentId);
        LktTranslation::createIfMissing('addUserAndNew', TranslationType::Text, [
            'es' => 'Agregar usuario y seguir',
            'en' => 'Add user and new',
        ], $parentId);
        LktTranslation::createIfMissing('titleSingle', TranslationType::Text, [
            'es' => 'Usuario',
            'en' => 'User',
        ], $parentId);
        LktTranslation::createIfMissing('titleMany', TranslationType::Text, [
            'es' => 'Usuarios',
            'en' => 'Users',
        ], $parentId);

        LktTranslation::createIfMissing('canReceiveMailNotifications', TranslationType::Text, [
            'es' => 'Recibir notificaciones por correo',
            'en' => 'Send mail notifications',
        ], $parentId);

        LktTranslation::createIfMissing('canReceivePushNotifications', TranslationType::Text, [
            'es' => 'Recibir notificaciones push',
            'en' => 'Send push notifications',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('canReceiveMailNotificationsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('0', TranslationType::Text, [
            'es' => 'Solo esenciales',
            'en' => 'Only essentials',
        ], $parentId);
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Todas',
            'en' => 'All',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('canReceivePushNotificationsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('0', TranslationType::Text, [
            'es' => 'Solo esenciales',
            'en' => 'Only essentials',
        ], $parentId);
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Todas',
            'en' => 'All',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('userRoleForm', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('name', TranslationType::Text, [
            'es' => 'Nombre',
            'en' => 'Name',
        ], $parentId);
        LktTranslation::createIfMissing('permissions', TranslationType::Text, [
            'es' => 'Permisos',
            'en' => 'Permissions',
        ], $parentId);
        LktTranslation::createIfMissing('component', TranslationType::Text, [
            'es' => 'Componente',
            'en' => 'Component',
        ], $parentId);
        LktTranslation::createIfMissing('ls', TranslationType::Text, [
            'es' => 'Listar elementos',
            'en' => 'List items',
        ], $parentId);
        LktTranslation::createIfMissing('mk', TranslationType::Text, [
            'es' => 'Crear elementos',
            'en' => 'Create items',
        ], $parentId);
        LktTranslation::createIfMissing('r', TranslationType::Text, [
            'es' => 'Leer elemento',
            'en' => 'Read item',
        ], $parentId);
        LktTranslation::createIfMissing('up', TranslationType::Text, [
            'es' => 'Actualizar elementos',
            'en' => 'Update items',
        ], $parentId);
        LktTranslation::createIfMissing('rm', TranslationType::Text, [
            'es' => 'Eliminar elementos',
            'en' => 'Drop items',
        ], $parentId);
        LktTranslation::createIfMissing('add', TranslationType::Text, [
            'es' => 'Agregar rol',
            'en' => 'Add role',
        ], $parentId);
        LktTranslation::createIfMissing('addAndNew', TranslationType::Text, [
            'es' => 'Agregar rol y seguir',
            'en' => 'Add role and new',
        ], $parentId);
        LktTranslation::createIfMissing('titleSingle', TranslationType::Text, [
            'es' => 'Rol de usuario',
            'en' => 'User role',
        ], $parentId);
        LktTranslation::createIfMissing('titleMany', TranslationType::Text, [
            'es' => 'Roles de usuario',
            'en' => 'User Roles',
        ], $parentId);



        $parent = LktTranslation::createIfMissing('menuEntryTypes', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'URL local',
            'en' => 'Local URL',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'URL completa',
            'en' => 'Full URL',
        ], $parentId);
        LktTranslation::createIfMissing('4', TranslationType::Text, [
            'es' => 'Listado de Páginas Web',
            'en' => 'Web Pages List',
        ], $parentId);
        LktTranslation::createIfMissing('6', TranslationType::Text, [
            'es' => 'Listado de Elementos Web',
            'en' => 'Web Items List',
        ], $parentId);
        LktTranslation::createIfMissing('8', TranslationType::Text, [
            'es' => 'Encabezado',
            'en' => 'Heading',
        ], $parentId);
        LktTranslation::createIfMissing('9', TranslationType::Text, [
            'es' => 'Submenú',
            'en' => 'Submenu',
        ], $parentId);
        LktTranslation::createIfMissing('10', TranslationType::Text, [
            'es' => 'Ruta de la app',
            'en' => 'App route',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('accessLevel', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Cualquiera',
            'en' => 'Any user',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Solo usuarios registrados',
            'en' => 'Only logged users',
        ], $parentId);
        LktTranslation::createIfMissing('3', TranslationType::Text, [
            'es' => 'Solo usuarios anónimos',
            'en' => 'Only anonymous users',
        ], $parentId);
        LktTranslation::createIfMissing('4', TranslationType::Text, [
            'es' => 'Solo administradores',
            'en' => 'Only admin users',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('webItems', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('user', TranslationType::Text, [
            'es' => 'Usuarios',
            'en' => 'Users',
        ], $parentId);
        LktTranslation::createIfMissing('user-role', TranslationType::Text, [
            'es' => 'Roles de usuarios',
            'en' => 'User Roles',
        ], $parentId);
        LktTranslation::createIfMissing('menu', TranslationType::Text, [
            'es' => 'Menus',
            'en' => 'Menus',
        ], $parentId);
        LktTranslation::createIfMissing('menu-entry', TranslationType::Text, [
            'es' => 'Entradas de Menus',
            'en' => 'Menu Entries',
        ], $parentId);
        LktTranslation::createIfMissing('i18n', TranslationType::Text, [
            'es' => 'Traducciones',
            'en' => 'Translations',
        ], $parentId);
        LktTranslation::createIfMissing('many-i18n', TranslationType::Text, [
            'es' => 'Diccionario',
            'en' => 'Dictionary',
        ], $parentId);
        LktTranslation::createIfMissing('contact-reason', TranslationType::Text, [
            'es' => 'Motivos de contacto',
            'en' => 'Contact reasons',
        ], $parentId);
        LktTranslation::createIfMissing('contact-request', TranslationType::Text, [
            'es' => 'Solicitudes de contacto',
            'en' => 'Contact requests',
        ], $parentId);
        LktTranslation::createIfMissing('contact', TranslationType::Text, [
            'es' => 'Contacto',
            'en' => 'Contact',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('webPages', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('page', TranslationType::Text, [
            'es' => 'Página',
            'en' => 'Page',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('webPageStatus', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Pública',
            'en' => 'Public',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Borrador',
            'en' => 'Draft',
        ], $parentId);
        LktTranslation::createIfMissing('3', TranslationType::Text, [
            'es' => 'Programada',
            'en' => 'Scheduled',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('webElement', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createOrUpdate('1', TranslationType::Text, [
            'es' => 'Texto libre',
            'en' => 'Free text',
        ], $parentId);
        LktTranslation::createOrUpdate('2', TranslationType::Text, [
            'es' => 'Caja de texto',
            'en' => 'Text box',
        ], $parentId);
        LktTranslation::createOrUpdate('3', TranslationType::Text, [
            'es' => 'Cuadrícula',
            'en' => 'Layout',
        ], $parentId);
        LktTranslation::createOrUpdate('4', TranslationType::Text, [
            'es' => 'Caja con cuadrícula',
            'en' => 'Layout box',
        ], $parentId);
        LktTranslation::createOrUpdate('5', TranslationType::Text, [
            'es' => 'Acordeón con texto',
            'en' => 'Text accordion',
        ], $parentId);
        LktTranslation::createOrUpdate('6', TranslationType::Text, [
            'es' => 'Acordeón con cuadrícula',
            'en' => 'Layout accordion',
        ], $parentId);
        LktTranslation::createOrUpdate('7', TranslationType::Text, [
            'es' => 'Banner',
            'en' => 'Banner',
        ], $parentId);
        LktTranslation::createOrUpdate('8', TranslationType::Text, [
            'es' => 'Banner con cuadrícula',
            'en' => 'Layout Banner',
        ], $parentId);
        LktTranslation::createOrUpdate('9', TranslationType::Text, [
            'es' => 'Enlace',
            'en' => 'Anchor',
        ], $parentId);
        LktTranslation::createOrUpdate('10', TranslationType::Text, [
            'es' => 'Botón',
            'en' => 'Button',
        ], $parentId);
        LktTranslation::createOrUpdate('11', TranslationType::Text, [
            'es' => 'Cabecera',
            'en' => 'Header',
        ], $parentId);
        LktTranslation::createOrUpdate('12', TranslationType::Text, [
            'es' => 'Icono',
            'en' => 'Icon',
        ], $parentId);
        LktTranslation::createOrUpdate('13', TranslationType::Text, [
            'es' => 'Iconos',
            'en' => 'Icons',
        ], $parentId);
        LktTranslation::createOrUpdate('14', TranslationType::Text, [
            'es' => 'Multimedia',
            'en' => 'Multimedia',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('layoutEngines', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('grid', TranslationType::Text, [
            'es' => 'CSS Grid',
            'en' => 'CSS Grid',
        ], $parentId);
        LktTranslation::createIfMissing('flex-row', TranslationType::Text, [
            'es' => 'CSS Flex: Única Fila',
            'en' => 'CSS Flex: Single Row',
        ], $parentId);
        LktTranslation::createIfMissing('flex-rows', TranslationType::Text, [
            'es' => 'CSS Flex: Filas',
            'en' => 'CSS Flex: Rows',
        ], $parentId);
        LktTranslation::createIfMissing('flex-column', TranslationType::Text, [
            'es' => 'CSS Flex: Columna',
            'en' => 'CSS Flex: Column',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('alignItemsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('lkt-align-items-start', TranslationType::Text, [
            'es' => 'Por defecto: Inicio',
            'en' => 'Default: Start',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-align-items-center', TranslationType::Text, [
            'es' => 'Por defecto: Centro',
            'en' => 'Default: Center',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-align-items-end', TranslationType::Text, [
            'es' => 'Por defecto: Final',
            'en' => 'Default: End',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-align-items-stretch', TranslationType::Text, [
            'es' => 'Por defecto: Estirado',
            'en' => 'Default: Stretch',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('flexColumnsOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('lkt-flex-col-1', TranslationType::Text, [
            'es' => 'Por defecto: 1',
            'en' => 'Default: 1',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-2', TranslationType::Text, [
            'es' => 'Por defecto: 2',
            'en' => 'Default: 2',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-3', TranslationType::Text, [
            'es' => 'Por defecto: 3',
            'en' => 'Default: 3',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-4', TranslationType::Text, [
            'es' => 'Por defecto: 4',
            'en' => 'Default: 4',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-5', TranslationType::Text, [
            'es' => 'Por defecto: 5',
            'en' => 'Default: 5',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-6', TranslationType::Text, [
            'es' => 'Por defecto: 6',
            'en' => 'Default: 6',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-7', TranslationType::Text, [
            'es' => 'Por defecto: 7',
            'en' => 'Default: 7',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-8', TranslationType::Text, [
            'es' => 'Por defecto: 8',
            'en' => 'Default: 8',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-9', TranslationType::Text, [
            'es' => 'Por defecto: 9',
            'en' => 'Default: 9',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-10', TranslationType::Text, [
            'es' => 'Por defecto: 10',
            'en' => 'Default: 10',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-11', TranslationType::Text, [
            'es' => 'Por defecto: 11',
            'en' => 'Default: 11',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-12', TranslationType::Text, [
            'es' => 'Por defecto: 12',
            'en' => 'Default: 12',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-1--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 1',
            'en' => 'From 768px: 1',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-2--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 2',
            'en' => 'From 768px: 2',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-3--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 3',
            'en' => 'From 768px: 3',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-4--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 4',
            'en' => 'From 768px: 4',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-flex-col-5--from-768', TranslationType::Text, [
            'es' => 'Desde 768px: 5',
            'en' => 'From 768px: 5',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('justifyContentOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('lkt-justify-content-stretch', TranslationType::Text, [
            'es' => 'Por defecto: Ajustado',
            'en' => 'Default: Stretch',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-justify-content-center', TranslationType::Text, [
            'es' => 'Por defecto: Centrado',
            'en' => 'Default: Center',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-justify-content-space-between', TranslationType::Text, [
            'es' => 'Por defecto: Separar elementos',
            'en' => 'Default: Space between',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-justify-content-space-around', TranslationType::Text, [
            'es' => 'Por defecto: Airear elementos',
            'en' => 'Default: Space around',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-justify-content-space-evenly', TranslationType::Text, [
            'es' => 'Por defecto: Espacio equitativo',
            'en' => 'Default: Space evenly',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-justify-content-start', TranslationType::Text, [
            'es' => 'Por defecto: Al inicio',
            'en' => 'Default: Start',
        ], $parentId);
        LktTranslation::createIfMissing('lkt-justify-content-end', TranslationType::Text, [
            'es' => 'Por defecto: Al final',
            'en' => 'Default: End',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('accordionTypeOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('auto', TranslationType::Text, [
            'es' => 'Por defecto',
            'en' => 'Default',
        ], $parentId);
        LktTranslation::createIfMissing('always', TranslationType::Text, [
            'es' => 'Siempre abierto',
            'en' => 'Always opened',
        ], $parentId);
        LktTranslation::createIfMissing('lazy', TranslationType::Text, [
            'es' => 'Carga lenta',
            'en' => 'Lazy load',
        ], $parentId);
        LktTranslation::createIfMissing('ever', TranslationType::Text, [
            'es' => 'Carga siempre',
            'en' => 'Ever load',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('bannerTypeOptions', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('static', TranslationType::Text, [
            'es' => 'Fondo estático',
            'en' => 'Static background',
        ], $parentId);
        LktTranslation::createIfMissing('parallax', TranslationType::Text, [
            'es' => 'Fondo paralelo',
            'en' => 'Parallax background',
        ], $parentId);

        $parent = LktTranslation::createIfMissing('months', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Enero',
            'en' => 'January',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Febrero',
            'en' => 'February',
        ], $parentId);
        LktTranslation::createIfMissing('3', TranslationType::Text, [
            'es' => 'Marzo',
            'en' => 'March',
        ], $parentId);
        LktTranslation::createIfMissing('4', TranslationType::Text, [
            'es' => 'Abril',
            'en' => 'April',
        ], $parentId);
        LktTranslation::createIfMissing('5', TranslationType::Text, [
            'es' => 'Mayo',
            'en' => 'May',
        ], $parentId);
        LktTranslation::createIfMissing('6', TranslationType::Text, [
            'es' => 'Junio',
            'en' => 'June',
        ], $parentId);
        LktTranslation::createIfMissing('7', TranslationType::Text, [
            'es' => 'Julio',
            'en' => 'July',
        ], $parentId);
        LktTranslation::createIfMissing('8', TranslationType::Text, [
            'es' => 'Agosto',
            'en' => 'August',
        ], $parentId);
        LktTranslation::createIfMissing('9', TranslationType::Text, [
            'es' => 'Septiembre',
            'en' => 'September',
        ], $parentId);
        LktTranslation::createIfMissing('10', TranslationType::Text, [
            'es' => 'Octubre',
            'en' => 'October',
        ], $parentId);
        LktTranslation::createIfMissing('11', TranslationType::Text, [
            'es' => 'Noviembre',
            'en' => 'November',
        ], $parentId);
        LktTranslation::createIfMissing('12', TranslationType::Text, [
            'es' => 'Diciembre',
            'en' => 'December',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('dayOfWeek', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Lunes',
            'en' => 'Monday',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Martes',
            'en' => 'Tuesday',
        ], $parentId);
        LktTranslation::createIfMissing('3', TranslationType::Text, [
            'es' => 'Miércoles',
            'en' => 'Wednesday',
        ], $parentId);
        LktTranslation::createIfMissing('4', TranslationType::Text, [
            'es' => 'Jueves',
            'en' => 'Thursday',
        ], $parentId);
        LktTranslation::createIfMissing('5', TranslationType::Text, [
            'es' => 'Viernes',
            'en' => 'Friday',
        ], $parentId);
        LktTranslation::createIfMissing('6', TranslationType::Text, [
            'es' => 'Sábado',
            'en' => 'Saturday',
        ], $parentId);
        LktTranslation::createIfMissing('7', TranslationType::Text, [
            'es' => 'Domingo',
            'en' => 'Sunday',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('dayOfWeekAbbr', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('1', TranslationType::Text, [
            'es' => 'Lu',
            'en' => 'Mo',
        ], $parentId);
        LktTranslation::createIfMissing('2', TranslationType::Text, [
            'es' => 'Ma',
            'en' => 'Tu',
        ], $parentId);
        LktTranslation::createIfMissing('3', TranslationType::Text, [
            'es' => 'Mi',
            'en' => 'We',
        ], $parentId);
        LktTranslation::createIfMissing('4', TranslationType::Text, [
            'es' => 'Ju',
            'en' => 'Th',
        ], $parentId);
        LktTranslation::createIfMissing('5', TranslationType::Text, [
            'es' => 'Vi',
            'en' => 'Fr',
        ], $parentId);
        LktTranslation::createIfMissing('6', TranslationType::Text, [
            'es' => 'Sa',
            'en' => 'Sa',
        ], $parentId);
        LktTranslation::createIfMissing('7', TranslationType::Text, [
            'es' => 'Do',
            'en' => 'Su',
        ], $parentId);


        $parent = LktTranslation::createIfMissing('buttons', TranslationType::Many, []);
        $parentId = $parent->getId();
        LktTranslation::createIfMissing('saveChanges', TranslationType::Text, [
            'es' => 'Guardar cambios',
            'en' => 'Save changes',
        ], $parentId);
        LktTranslation::createIfMissing('save', TranslationType::Text, [
            'es' => 'Guardar',
            'en' => 'Save',
        ], $parentId);
        LktTranslation::createIfMissing('drop', TranslationType::Text, [
            'es' => 'Eliminar',
            'en' => 'Delete',
        ], $parentId);
        LktTranslation::createIfMissing('back', TranslationType::Text, [
            'es' => 'Atrás',
            'en' => 'Back',
        ], $parentId);
        LktTranslation::createIfMissing('cancel', TranslationType::Text, [
            'es' => 'Cancelar',
            'en' => 'Cancel',
        ], $parentId);
        LktTranslation::createIfMissing('duplicate', TranslationType::Text, [
            'es' => 'Duplicar',
            'en' => 'Duplicate',
        ], $parentId);
        LktTranslation::createIfMissing('confirm', TranslationType::Text, [
            'es' => 'Confirmar',
            'en' => 'Confirm',
        ], $parentId);
        LktTranslation::createIfMissing('accept', TranslationType::Text, [
            'es' => 'Aceptar',
            'en' => 'Accept',
        ], $parentId);
        LktTranslation::createIfMissing('details', TranslationType::Text, [
            'es' => 'Detalles',
            'en' => 'Details',
        ], $parentId);
        LktTranslation::createIfMissing('editModeOn', TranslationType::Text, [
            'es' => 'Detener edición',
            'en' => 'Disable edition',
        ], $parentId);
        LktTranslation::createIfMissing('editModeOff', TranslationType::Text, [
            'es' => 'Editar',
            'en' => 'Enable edition',
        ], $parentId);
        LktTranslation::createIfMissing('actions', TranslationType::Text, [
            'es' => 'Acciones',
            'en' => 'Actions',
        ], $parentId);
        LktTranslation::createIfMissing('addElement', TranslationType::Text, [
            'es' => 'Añadir otro',
            'en' => 'Add element',
        ], $parentId);
        LktTranslation::createIfMissing('check', TranslationType::Text, [
            'es' => 'Comprobar',
            'en' => 'Check',
        ], $parentId);
        LktTranslation::createIfMissing('unlinkElement', TranslationType::Text, [
            'es' => 'Desvincular',
            'en' => 'Unlink',
        ], $parentId);
        LktTranslation::createIfMissing('add', TranslationType::Text, [
            'es' => 'Agregar',
            'en' => 'Add',
        ], $parentId);
        LktTranslation::createIfMissing('addAndNew', TranslationType::Text, [
            'es' => 'Agregar y otro',
            'en' => 'Add and other',
        ], $parentId);
    }
}