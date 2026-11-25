@extends('errors.layout')

@section('code', '503')
@section('title', __('Service Unavailable'))
@section('message', __('The system is temporarily unavailable for maintenance. Try again in a few minutes.'))
@section('retry', true)
