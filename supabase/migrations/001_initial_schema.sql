-- Initial Schema Migration for SmarboPlusEvent

-- Profiles table to store additional user information and roles
CREATE TABLE public.profiles (
    id UUID REFERENCES auth.users ON DELETE CASCADE PRIMARY KEY,
    username TEXT UNIQUE NOT NULL,
    full_name TEXT,
    phone TEXT,
    avatar_url TEXT,
    role TEXT NOT NULL DEFAULT 'event_owner' CHECK (role IN ('admin', 'event_owner')),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS on profiles
ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;

-- Events table
CREATE TABLE public.events (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    event_name TEXT NOT NULL,
    event_type TEXT NOT NULL CHECK (event_type IN ('harusi', 'sendoff', 'kitchen_party', 'nyingine')),
    event_date DATE NOT NULL,
    location TEXT NOT NULL,
    description TEXT,
    cover_image TEXT,
    event_owner_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    created_by UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS on events
ALTER TABLE public.events ENABLE ROW LEVEL SECURITY;

-- Event Posts table
CREATE TABLE public.event_posts (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    event_id UUID REFERENCES public.events(id) ON DELETE CASCADE NOT NULL,
    title TEXT NOT NULL,
    content TEXT,
    post_type TEXT DEFAULT 'update' CHECK (post_type IN ('update', 'photo', 'video', 'announcement')),
    media_url TEXT,
    media_type TEXT DEFAULT 'image' CHECK (media_type IN ('image', 'video')),
    posted_by UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS on event_posts
ALTER TABLE public.event_posts ENABLE ROW LEVEL SECURITY;

-- Event Post Media table
CREATE TABLE public.event_post_media (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    post_id UUID REFERENCES public.event_posts(id) ON DELETE CASCADE NOT NULL,
    media_url TEXT NOT NULL,
    media_type TEXT DEFAULT 'image' CHECK (media_type IN ('image', 'video')),
    thumbnail_url TEXT,
    caption TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS on event_post_media
ALTER TABLE public.event_post_media ENABLE ROW LEVEL SECURITY;

-- Contributions table
CREATE TABLE public.contributions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    event_id UUID REFERENCES public.events(id) ON DELETE CASCADE NOT NULL,
    contributor_name TEXT NOT NULL,
    phone_number TEXT,
    promised_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved')),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Computed column for balance (PostgreSQL view or generated column)
ALTER TABLE public.contributions ADD COLUMN balance DECIMAL(12,2) GENERATED ALWAYS AS (promised_amount - paid_amount) STORED;

-- Enable RLS on contributions
ALTER TABLE public.contributions ENABLE ROW LEVEL SECURITY;

-- Invitations table
CREATE TABLE public.invitations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    event_id UUID REFERENCES public.events(id) ON DELETE CASCADE NOT NULL,
    guest_name TEXT NOT NULL,
    phone_number TEXT,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'disapproved')),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS on invitations
ALTER TABLE public.invitations ENABLE ROW LEVEL SECURITY;

-- Attendance table
CREATE TABLE public.attendance (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    event_id UUID REFERENCES public.events(id) ON DELETE CASCADE NOT NULL,
    guest_name TEXT NOT NULL,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved')),
    attended_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS on attendance
ALTER TABLE public.attendance ENABLE ROW LEVEL SECURITY;

-- Event Post Likes table
CREATE TABLE public.event_post_likes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    post_id UUID REFERENCES public.event_posts(id) ON DELETE CASCADE NOT NULL,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(post_id, user_id)
);

-- Enable RLS on event_post_likes
ALTER TABLE public.event_post_likes ENABLE ROW LEVEL SECURITY;

-- Event Post Comments table
CREATE TABLE public.event_post_comments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    post_id UUID REFERENCES public.event_posts(id) ON DELETE CASCADE NOT NULL,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    comment TEXT NOT NULL,
    parent_id UUID REFERENCES public.event_post_comments(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable RLS on event_post_comments
ALTER TABLE public.event_post_comments ENABLE ROW LEVEL SECURITY;

-- Functions for profile management
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO public.profiles (id, username, full_name, avatar_url, role)
    VALUES (
        new.id,
        COALESCE(new.raw_user_meta_data->>'username', new.email),
        new.raw_user_meta_data->>'full_name',
        new.raw_user_meta_data->>'avatar_url',
        COALESCE(new.raw_user_meta_data->>'role', 'event_owner')
    );
    RETURN new;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Trigger to create profile on signup
CREATE TRIGGER on_auth_user_created
    AFTER INSERT ON auth.users
    FOR EACH ROW EXECUTE FUNCTION public.handle_new_user();

-- RPC Functions for dashboard stats
CREATE OR REPLACE FUNCTION public.sum_promised_amount()
RETURNS DECIMAL AS $$
BEGIN
    RETURN (SELECT COALESCE(SUM(promised_amount), 0) FROM public.contributions);
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION public.sum_paid_amount()
RETURNS DECIMAL AS $$
BEGIN
    RETURN (SELECT COALESCE(SUM(paid_amount), 0) FROM public.contributions);
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- RPC for event owner stats
CREATE OR REPLACE FUNCTION public.sum_owner_promised_amount(owner_uuid UUID)
RETURNS DECIMAL AS $$
BEGIN
    RETURN (
        SELECT COALESCE(SUM(c.promised_amount), 0)
        FROM public.contributions c
        JOIN public.events e ON c.event_id = e.id
        WHERE e.event_owner_id = owner_uuid
    );
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION public.sum_owner_paid_amount(owner_uuid UUID)
RETURNS DECIMAL AS $$
BEGIN
    RETURN (
        SELECT COALESCE(SUM(c.paid_amount), 0)
        FROM public.contributions c
        JOIN public.events e ON c.event_id = e.id
        WHERE e.event_owner_id = owner_uuid
    );
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
