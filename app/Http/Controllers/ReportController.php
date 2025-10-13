<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Создать жалобу на контент
     */
    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|string',
            'reportable_id' => 'required|integer',
            'type' => ['required', Rule::in(array_keys(Report::getTypes()))],
            'reason' => 'nullable|string|max:1000',
            'content_url' => 'nullable|string|max:500'
        ]);

        // Проверяем, что пользователь авторизован
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необхідно увійти в систему для подачі скарги'
            ], 401);
        }

        // Получаем модель контента
        $reportableType = $request->reportable_type;
        $reportableId = $request->reportable_id;

        // Валидируем тип контента
        $allowedTypes = [
            'App\Models\Review',
            'App\Models\Discussion',
            'App\Models\DiscussionReply',
            'App\Models\Quote',
            'App\Models\Fact'
        ];

        if (!in_array($reportableType, $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Недопустимий тип контента'
            ], 400);
        }

        // Находим контент
        $reportable = $reportableType::find($reportableId);
        if (!$reportable) {
            return response()->json([
                'success' => false,
                'message' => 'Контент не знайдений'
            ], 404);
        }

        // Проверяем, не подавал ли пользователь уже жалобу на этот контент
        $existingReport = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Ви уже подали скаргу на цей контент'
            ], 400);
        }

        // Получаем пользователя, на которого жалуемся
        $reportedUserId = null;
        if (isset($reportable->user_id)) {
            $reportedUserId = $reportable->user_id;
        }

        // Создаем жалобу
        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reported_user_id' => $reportedUserId,
            'reportable_type' => $reportableType,
            'reportable_id' => $reportableId,
            'type' => $request->type,
            'reason' => $request->reason,
            'content_url' => $request->content_url,
            'status' => Report::STATUS_PENDING,
            'metadata' => [
                'content_preview' => $this->getContentPreview($reportable),
                'reported_at' => now()->toISOString()
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Скарга успішно відправлена. Ми розглянемо її в найближчий час.',
            'report' => $report
        ]);
    }

    /**
     * Получить предварительный просмотр контента для жалобы
     */
    private function getContentPreview($reportable): string
    {
        if (isset($reportable->content)) {
            return mb_substr(strip_tags($reportable->content), 0, 200);
        } elseif (isset($reportable->title)) {
            return $reportable->title;
        }
        
        return 'Контент';
    }

    /**
     * Получить все жалобы (только для модераторов)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Report::class);

        $query = Report::with(['reporter', 'reportedUser', 'moderator', 'reportable'])
            ->orderBy('created_at', 'desc');

        // Фильтрация по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Фильтрация по типу
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $reports = $query->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Показать конкретную жалобу (только для модераторов)
     */
    public function show(Report $report)
    {
        $this->authorize('view', $report);

        $report->load(['reporter', 'reportedUser', 'moderator', 'reportable']);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Обновить статус жалобы (только для модераторов)
     */
    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $request->validate([
            'status' => ['required', Rule::in(array_keys(Report::getStatuses()))],
            'moderator_comment' => 'nullable|string|max:1000'
        ]);

        $report->update([
            'status' => $request->status,
            'moderator_id' => Auth::id(),
            'moderator_comment' => $request->moderator_comment,
            'processed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Статус скарги оновлений',
            'report' => $report
        ]);
    }

    /**
     * Получить типы жалоб для формы
     */
    public function getTypes()
    {
        return response()->json([
            'types' => Report::getTypes()
        ]);
    }
}
