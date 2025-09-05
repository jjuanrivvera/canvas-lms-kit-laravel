<?php

namespace CanvasLMS\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Canvas Facade for Laravel.
 *
 * Provides a convenient static interface to the Canvas LMS Kit.
 *
 * @method static \CanvasLMS\Laravel\CanvasManager          connection(string $name)
 * @method static string                                    getConnection()
 * @method static array|null                                getConnectionConfig()
 * @method static array                                     getAvailableConnections()
 * @method static mixed                                     usingConnection(string $connection, callable $callback)
 * @method static \CanvasLMS\Api\Courses\Course             courses()
 * @method static \CanvasLMS\Api\Courses\Course             course(int $id)
 * @method static \CanvasLMS\Api\Users\User                 users()
 * @method static \CanvasLMS\Api\Users\User                 user(int $id)
 * @method static \CanvasLMS\Api\Enrollments\Enrollment     enrollments()
 * @method static \CanvasLMS\Api\Enrollments\Enrollment     enrollment(int $id)
 * @method static \CanvasLMS\Api\Assignments\Assignment     assignments()
 * @method static \CanvasLMS\Api\Assignments\Assignment     assignment(int $courseId, int $id)
 * @method static \CanvasLMS\Api\Modules\Module             modules()
 * @method static \CanvasLMS\Api\Modules\Module             module(int $courseId, int $id)
 * @method static \CanvasLMS\Api\Pages\Page                 pages()
 * @method static \CanvasLMS\Api\Pages\Page                 page(int $courseId, string $url)
 * @method static \CanvasLMS\Api\Files\File                 files()
 * @method static \CanvasLMS\Api\Files\File                 file(int $id)
 * @method static \CanvasLMS\Api\Folders\Folder             folders()
 * @method static \CanvasLMS\Api\Folders\Folder             folder(int $id)
 * @method static \CanvasLMS\Api\Groups\Group               groups()
 * @method static \CanvasLMS\Api\Groups\Group               group(int $id)
 * @method static \CanvasLMS\Api\Sections\Section           sections()
 * @method static \CanvasLMS\Api\Sections\Section           section(int $id)
 * @method static \CanvasLMS\Api\Accounts\Account           accounts()
 * @method static \CanvasLMS\Api\Accounts\Account           account(int $id)
 * @method static \CanvasLMS\Api\Roles\Role                 roles()
 * @method static \CanvasLMS\Api\Roles\Role                 role(int $accountId, int $id)
 * @method static \CanvasLMS\Api\Admins\Admin               admins()
 * @method static \CanvasLMS\Api\Analytics\Analytics        analytics()
 * @method static \CanvasLMS\Api\Conversations\Conversation conversations()
 * @method static \CanvasLMS\Api\Conversations\Conversation conversation(int $id)
 *
 * @see \CanvasLMS\Laravel\CanvasManager
 */
class Canvas extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'canvas';
    }
}
