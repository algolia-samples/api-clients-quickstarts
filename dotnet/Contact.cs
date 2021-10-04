using System;

namespace DotNetQuickStart
{
    public class Contact
    {
        public string Name { get; set; }

        public string ObjectID { get; set; }

        public string Email { get; set; }

        public override string ToString()
        {
            return $"Name: {Name}, ObjectId: {ObjectID}";
        }
    }
}