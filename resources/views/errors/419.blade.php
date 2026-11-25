@extends('errors.layout')

@section('code', '419')
@section('title', __('Page Expired'))
@section('message', __('Your session has expired due to inactivity. Reload the page and try again.'))
@section('retry', true)
