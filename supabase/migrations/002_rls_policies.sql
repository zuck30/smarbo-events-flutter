-- RLS Policies Migration for SmarboPlusEvent

-- 1. Profiles Policies
CREATE POLICY "Public profiles are viewable by everyone"
ON public.profiles FOR SELECT USING (true);

CREATE POLICY "Users can update own profile"
ON public.profiles FOR UPDATE USING (auth.uid() = id);

CREATE POLICY "Admins can manage all profiles"
ON public.profiles FOR ALL USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);

-- 2. Events Policies
CREATE POLICY "Admins can manage all events"
ON public.events FOR ALL USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);

CREATE POLICY "Owners can see and manage their own events"
ON public.events FOR ALL USING (
    event_owner_id = auth.uid() OR created_by = auth.uid()
);

-- 3. Event Posts Policies
CREATE POLICY "Admins can manage all posts"
ON public.event_posts FOR ALL USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);

CREATE POLICY "Owners can manage posts for their events"
ON public.event_posts FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.events
        WHERE id = event_posts.event_id
        AND (event_owner_id = auth.uid() OR created_by = auth.uid())
    )
);

CREATE POLICY "Everyone can see posts for events they have access to"
ON public.event_posts FOR SELECT USING (true);

-- 4. Event Post Media Policies
CREATE POLICY "Admins can manage all post media"
ON public.event_post_media FOR ALL USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);

CREATE POLICY "Owners can manage media for their posts"
ON public.event_post_media FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.event_posts ep
        JOIN public.events e ON ep.event_id = e.id
        WHERE ep.id = event_post_media.post_id
        AND (e.event_owner_id = auth.uid() OR e.created_by = auth.uid())
    )
);

CREATE POLICY "Everyone can see post media"
ON public.event_post_media FOR SELECT USING (true);

-- 5. Contributions Policies
CREATE POLICY "Admins can manage all contributions"
ON public.contributions FOR ALL USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);

CREATE POLICY "Owners can manage contributions for their events"
ON public.contributions FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.events
        WHERE id = contributions.event_id
        AND (event_owner_id = auth.uid() OR created_by = auth.uid())
    )
);

-- 6. Invitations Policies
CREATE POLICY "Admins can manage all invitations"
ON public.invitations FOR ALL USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);

CREATE POLICY "Owners can manage invitations for their events"
ON public.invitations FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.events
        WHERE id = invitations.event_id
        AND (event_owner_id = auth.uid() OR created_by = auth.uid())
    )
);

-- 7. Attendance Policies
CREATE POLICY "Admins can manage all attendance"
ON public.attendance FOR ALL USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);

CREATE POLICY "Owners can manage attendance for their events"
ON public.attendance FOR ALL USING (
    EXISTS (
        SELECT 1 FROM public.events
        WHERE id = attendance.event_id
        AND (event_owner_id = auth.uid() OR created_by = auth.uid())
    )
);

-- 8. Event Post Likes Policies
CREATE POLICY "Users can manage their own likes"
ON public.event_post_likes FOR ALL USING (user_id = auth.uid());

CREATE POLICY "Everyone can see likes"
ON public.event_post_likes FOR SELECT USING (true);

-- 9. Event Post Comments Policies
CREATE POLICY "Users can manage their own comments"
ON public.event_post_comments FOR ALL USING (user_id = auth.uid());

CREATE POLICY "Everyone can see comments"
ON public.event_post_comments FOR SELECT USING (true);

CREATE POLICY "Admins can delete any comment"
ON public.event_post_comments FOR DELETE USING (
    (SELECT role FROM public.profiles WHERE id = auth.uid()) = 'admin'
);
