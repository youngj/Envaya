<?php

class Controller_Report extends Controller_Profile
{
    protected $report;

    function before()
    {
        parent::before();

        $reportId = $this->request->param('id');

        if ($reportId == 'new')
        {
            $this->request->action = 'new';
            return;
        }

        $report = $this->org->queryReports()->where('u.guid = ?', $reportId)->get();
        if ($report)
        {
            $this->report = $report;
            return;
        }
        else
        {
            $this->use_public_layout();
            $this->org_page_not_found();
        }
    }

    function action_index()
    {
        $org = $this->org;
        $report = $this->report;

        $this->use_public_layout();

        if ($report->canEdit())
        {
            add_submenu_item(__("report:edit"), "{$report->getURL()}/edit", 'edit');
        }

        $title = $report->getTitle();

        if (!$org->canView())
        {
            $org->showCantViewMessage();
            $body = '';
        }
        else
        {
            $body = $this->org_view_body($title, view('reports/view', array('report' => $report)));
        }

        $this->page_draw($title,$body);
    }

    function action_edit()
    {
        $this->require_editor();
        $report = $this->report;

        $title = __('report:edit');

        $cancelUrl = get_input('from') ?: $report->getURL();

        add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $area1 = view('reports/edit', array('report' => $report));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function action_save()
    {
        $this->require_editor();
        $this->validate_security_token();
        $report = $this->report;

        $report->saveInput();

        forward($report->getURL());        
    }

    function action_new()
    {
        $this->require_editor();
        $this->validate_security_token();
    }
}